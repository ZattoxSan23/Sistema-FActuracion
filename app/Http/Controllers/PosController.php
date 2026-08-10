<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Empresa;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Serie;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Services\Sunat\SunatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(private SunatService $sunat)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $caja = Caja::cajaAbierta();

        if (!$caja && !$user->isAdmin()) {
            return redirect()->route('caja.apertura');
        }

        $categorias = Categoria::activos()->ordenados()->get();
        $productos = Producto::visiblesPos()->with('categoria')->orderBy('orden')->get();
        $clienteDefecto = Cliente::getOrCreateVarios();
        $empresa = Empresa::actual();
        $seriesBoleta = Serie::where('tipo_comprobante', Serie::TIPO_BOLETA)->where('activo', true)->get();
        $seriesFactura = Serie::where('tipo_comprobante', Serie::TIPO_FACTURA)->where('activo', true)->get();

        return view('pos.index', compact(
            'caja', 'categorias', 'productos', 'clienteDefecto', 'empresa', 'seriesBoleta', 'seriesFactura'
        ));
    }

    public function buscarProductos(Request $request)
    {
        $termino = $request->get('q', '');
        $categoriaId = $request->get('categoria_id');

        $productos = Producto::visiblesPos()
            ->porCategoria($categoriaId)
            ->buscar($termino)
            ->orderBy('nombre')
            ->limit(50)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'codigo' => $p->codigo,
                'codigo_barra' => $p->codigo_barra,
                'nombre' => $p->nombre,
                'precio' => (float) $p->precio_venta,
                'incluye_igv' => $p->incluye_igv,
                'tipo_afectacion_igv' => $p->tipo_afectacion_igv,
                'unidad_medida' => $p->unidad_medida,
                'categoria' => $p->categoria?->nombre,
            ]);

        return response()->json($productos);
    }

    public function buscarClientes(Request $request)
    {
        $termino = $request->get('q', '');

        $clientes = Cliente::activos()
            ->buscar($termino)
            ->orderBy('nombre_razon_social')
            ->limit(20)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'text' => "{$c->tipo_documento}: {$c->numero_documento} - {$c->nombre_razon_social}",
                'documento' => $c->documento_completo,
                'nombre' => $c->nombre_razon_social,
                'direccion' => $c->direccion,
                'tipo_documento' => $c->tipo_documento,
                'numero_documento' => $c->numero_documento,
            ]);

        return response()->json(['results' => $clientes]);
    }

    public function clienteRapido(Request $request)
    {
        $data = $request->validate([
            'tipo_documento' => 'required|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento' => 'required|string|max:15',
            'nombre_razon_social' => 'required|string|max:200',
            'direccion' => 'nullable|string',
        ]);

        $cliente = Cliente::updateOrCreate(
            ['tipo_documento' => $data['tipo_documento'], 'numero_documento' => $data['numero_documento']],
            [
                'nombre_razon_social' => $data['nombre_razon_social'],
                'direccion' => $data['direccion'] ?? null,
                'activo' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'cliente' => [
                'id' => $cliente->id,
                'text' => $cliente->documento_completo . ' - ' . $cliente->nombre_razon_social,
                'nombre' => $cliente->nombre_razon_social,
                'direccion' => $cliente->direccion,
            ],
        ]);
    }

    public function storeVenta(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_comprobante' => 'required|in:01,03',
            'serie' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.001',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.descuento' => 'nullable|numeric|min:0',
            'pagos' => 'required|array|min:1',
            'pagos.*.metodo_pago' => 'required|in:efectivo,tarjeta,yape,plin,transferencia,otros',
            'pagos.*.monto' => 'required|numeric|min:0',
        ]);

        $user = $request->user();
        $caja = Caja::cajaAbierta();

        if (!$caja && !$user->isAdmin()) {
            throw ValidationException::withMessages(['error' => 'Debes abrir caja para registrar ventas.']);
        }

        try {
            $venta = DB::transaction(function () use ($request, $user, $caja) {
                $empresa = Empresa::actual();
                $igvPorcentaje = (float) ($empresa?->igv ?? 18);

                // 1. Obtener siguiente correlativo
                $correlativo = Serie::siguienteCorrelativo($request->tipo_comprobante, $request->serie);

                // 2. Calcular totales
                $opGravadas = 0;
                $opExoneradas = 0;
                $opInafectas = 0;

                $itemsProcesados = [];
                foreach ($request->items as $itemData) {
                    $producto = Producto::findOrFail($itemData['producto_id']);
                    $cantidad = (float) $itemData['cantidad'];
                    $descuentoItem = (float) ($itemData['descuento'] ?? 0);

                    $precioUnitarioConIgv = (float) $itemData['precio_unitario'];

                    if ($producto->incluye_igv) {
                        $precioUnitarioSinIgv = round($precioUnitarioConIgv / (1 + ($igvPorcentaje / 100)), 4);
                    } else {
                        $precioUnitarioSinIgv = $precioUnitarioConIgv;
                    }

                    $subtotal = round($cantidad * $precioUnitarioSinIgv, 2);
                    $igvItem = round($cantidad * ($precioUnitarioConIgv - $precioUnitarioSinIgv), 2);
                    $totalItem = round($cantidad * $precioUnitarioConIgv - $descuentoItem, 2);

                    $itemsProcesados[] = [
                        'producto' => $producto,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitarioSinIgv,
                        'precio_unitario_con_igv' => $precioUnitarioConIgv,
                        'descuento' => $descuentoItem,
                        'subtotal' => $subtotal,
                        'igv_item' => $igvItem,
                        'total_item' => $totalItem,
                    ];

                    if ($producto->tipo_afectacion_igv === Producto::AFECT_GRAVADO) {
                        $opGravadas += $subtotal;
                    } elseif ($producto->tipo_afectacion_igv === Producto::AFECT_EXONERADO) {
                        $opExoneradas += $subtotal;
                    } elseif ($producto->tipo_afectacion_igv === Producto::AFECT_INAFECTO) {
                        $opInafectas += $subtotal;
                    }
                }

                $descuentoGlobal = (float) $request->input('descuento_global', 0);
                $igvTotal = round($opGravadas * ($igvPorcentaje / 100), 2);
                $total = round($opGravadas + $opExoneradas + $opInafectas + $igvTotal, 2);

                // 3. Crear venta
                $venta = Venta::create([
                    'caja_id' => $caja?->id,
                    'cliente_id' => $request->cliente_id,
                    'user_id' => $user->id,
                    'correlativo' => $correlativo['correlativo_completo'],
                    'tipo_comprobante' => $request->tipo_comprobante,
                    'serie' => $correlativo['serie'],
                    'numero' => $correlativo['correlativo'],
                    'fecha_emision' => now(),
                    'moneda' => 'PEN',
                    'op_gravadas' => round($opGravadas, 2),
                    'op_exoneradas' => round($opExoneradas, 2),
                    'op_inafectas' => round($opInafectas, 2),
                    'descuento_global' => $descuentoGlobal,
                    'igv' => $igvTotal,
                    'total' => $total,
                    'estado' => 'registrada',
                    'estado_sunat' => 'pendiente',
                    'observaciones' => $request->input('observaciones'),
                ]);

                // 4. Crear items
                foreach ($itemsProcesados as $index => $item) {
                    VentaItem::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item['producto']->id,
                        'orden' => $index + 1,
                        'unidad_medida' => $item['producto']->unidad_medida,
                        'codigo_producto' => $item['producto']->codigo,
                        'descripcion' => $item['producto']->nombre,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'precio_unitario_con_igv' => $item['precio_unitario_con_igv'],
                        'valor_unitario' => $item['precio_unitario'],
                        'descuento' => $item['descuento'],
                        'subtotal' => $item['subtotal'],
                        'igv_item' => $item['igv_item'],
                        'total_item' => $item['total_item'],
                        'tipo_afectacion_igv' => $item['producto']->tipo_afectacion_igv,
                    ]);
                }

                // 5. Registrar pagos
                $montoRecibido = 0;
                $vueltoTotal = 0;
                foreach ($request->pagos as $pagoData) {
                    $monto = (float) $pagoData['monto'];
                    $vuelto = (float) ($pagoData['vuelto'] ?? 0);

                    Pago::create([
                        'venta_id' => $venta->id,
                        'metodo_pago' => $pagoData['metodo_pago'],
                        'monto' => $monto,
                        'vuelto' => $vuelto,
                        'monto_recibido' => $pagoData['monto_recibido'] ?? null,
                        'tipo_tarjeta' => $pagoData['tipo_tarjeta'] ?? null,
                        'marca_tarjeta' => $pagoData['marca_tarjeta'] ?? null,
                        'numero_operacion' => $pagoData['numero_operacion'] ?? null,
                        'numero_voucher' => $pagoData['numero_voucher'] ?? null,
                        'cuenta_destino' => $pagoData['cuenta_destino'] ?? null,
                        'banco' => $pagoData['banco'] ?? null,
                    ]);

                    if ($pagoData['metodo_pago'] === 'efectivo') {
                        $montoRecibido += $monto;
                    }
                }

                // 6. Actualizar totales de caja
                if ($caja) {
                    $caja->recalcularTotales();
                }

                return $venta->fresh(['items', 'pagos', 'cliente', 'usuario']);
            });

            // 7. Crear comprobante y enviar a SUNAT (asíncrono o directo)
            $this->procesarComprobante($venta);

            return response()->json([
                'success' => true,
                'venta' => [
                    'id' => $venta->id,
                    'correlativo' => $venta->correlativo,
                    'total' => $venta->total,
                ],
                'message' => 'Venta registrada correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    private function procesarComprobante(Venta $venta): void
    {
        try {
            // Crear registro de comprobante
            $comprobante = Comprobante::create([
                'venta_id' => $venta->id,
                'tipo_comprobante' => $venta->tipo_comprobante,
                'serie' => $venta->serie,
                'correlativo' => $venta->numero,
                'correlativo_completo' => $venta->correlativo,
                'estado' => Comprobante::ESTADO_BORRADOR,
            ]);

            // Generar y firmar XML
            $this->sunat->generarYEnviar($venta, $comprobante);
        } catch (\Exception $e) {
            \Log::error('Error al procesar comprobante: ' . $e->getMessage(), [
                'venta_id' => $venta->id,
            ]);
        }
    }

    public function imprimirTicket(Venta $venta)
    {
        $empresa = Empresa::actual();
        return view('pos.ticket', compact('venta', 'empresa'));
    }

    public function imprimirPdf(Venta $venta)
    {
        $empresa = Empresa::actual();
        $pdf = \PDF::loadView('pos.pdf-a4', compact('venta', 'empresa'));
        return $pdf->stream("comprobante-{$venta->correlativo}.pdf");
    }
}
