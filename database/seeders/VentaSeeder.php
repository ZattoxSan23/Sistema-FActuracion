<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Pago;
use App\Models\Serie;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $productos = DB::table('productos')->get();
        if ($productos->isEmpty()) {
            return;
        }

        $clientes = Cliente::where('tipo_documento', '!=', Cliente::TIPO_SIN_DOCUMENTO)->get();
        $clienteVarios = Cliente::where('tipo_documento', Cliente::TIPO_SIN_DOCUMENTO)->first();
        $cajeros = User::whereIn('rol', [User::ROL_CAJERA, User::ROL_ADMIN])->pluck('id')->toArray();
        if (empty($cajeros)) {
            $cajeros = User::pluck('id')->toArray();
        }

        $serieBoleta = Serie::where('tipo_comprobante', Serie::TIPO_BOLETA)->first();
        $serieFactura = Serie::where('tipo_comprobante', Serie::TIPO_FACTURA)->first();

        $metodos = [
            'efectivo' => 60,
            'tarjeta' => 20,
            'yape' => 12,
            'plin' => 5,
            'transferencia' => 3,
        ];

        $start = Carbon::now()->subYear()->startOfDay();
        $end = Carbon::now()->subDay()->endOfDay();
        $correlativoBoleta = 1;
        $correlativoFactura = 1;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayOfWeek = (int) $date->dayOfWeek;
            $month = (int) $date->month;

            $baseVentas = match (true) {
                $dayOfWeek === 0 => 8, $dayOfWeek === 6 => 12, $dayOfWeek === 1 => 15, default => 18,
            };
            if (in_array($month, [12, 7])) {
                $baseVentas = (int) ($baseVentas * 1.4);
            } elseif (in_array($month, [1, 2, 3])) {
                $baseVentas = (int) ($baseVentas * 0.75);
            }
            $numVentas = max(2, (int) ($baseVentas + random_int(-3, 4)));

            for ($i = 0; $i < $numVentas; $i++) {
                $hora = $this->horaPicoCafeteria();
                $minuto = random_int(0, 59);
                $segundo = random_int(0, 59);
                $fechaEmision = $date->copy()->setTime($hora, $minuto, $segundo);

                $usarFactura = random_int(1, 100) <= 25;
                $serie = $usarFactura ? $serieFactura : $serieBoleta;
                if (!$serie) {
                    $serie = $serieBoleta;
                    $usarFactura = false;
                }
                $correlativo = $usarFactura ? $correlativoFactura++ : $correlativoBoleta++;
                $numero = str_pad((string) $correlativo, 8, '0', STR_PAD_LEFT);
                $tipoComprobante = $usarFactura ? '01' : '03';
                $correlativoCompleto = $serie->serie.'-'.$numero;

                $numItems = random_int(1, 4);
                $itemsData = [];
                $productosUsados = $productos->shuffle()->take($numItems);

                foreach ($productosUsados as $p) {
                    $cantidad = random_int(1, 3);
                    $precioUnitario = (float) $p->precio_venta;
                    $precioSinIgv = round($precioUnitario / 1.18, 4);
                    $igvItem = round($cantidad * ($precioUnitario - $precioSinIgv), 2);
                    $subtotal = round($cantidad * $precioSinIgv, 2);
                    $totalItem = round($cantidad * $precioUnitario, 2);
                    $itemsData[] = [
                        'producto' => $p,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'precio_unitario_con_igv' => $precioUnitario,
                        'subtotal' => $subtotal,
                        'igv_item' => $igvItem,
                        'total_item' => $totalItem,
                    ];
                }

                $opGravadas = array_sum(array_column($itemsData, 'subtotal'));
                $igvTotal = array_sum(array_column($itemsData, 'igv_item'));
                $total = array_sum(array_column($itemsData, 'total_item'));

                $esClienteFactura = $usarFactura && $clientes->where('tipo_documento', 'RUC')->isNotEmpty();
                $clienteId = $esClienteFactura
                    ? $clientes->where('tipo_documento', 'RUC')->random()->id
                    : ($clienteVarios?->id ?? $clientes->first()?->id);

                $cajeroId = $cajeros[array_rand($cajeros)];

                $venta = Venta::create([
                    'caja_id' => null,
                    'cliente_id' => $clienteId,
                    'user_id' => $cajeroId,
                    'correlativo' => $correlativoCompleto,
                    'tipo_comprobante' => $tipoComprobante,
                    'serie' => $serie->serie,
                    'numero' => $correlativo,
                    'fecha_emision' => $fechaEmision,
                    'moneda' => 'PEN',
                    'op_gravadas' => $opGravadas,
                    'op_exoneradas' => 0,
                    'op_inafectas' => 0,
                    'descuento_global' => 0,
                    'igv' => $igvTotal,
                    'total' => $total,
                    'estado' => 'registrada',
                    'estado_sunat' => 'aceptado',
                    'observaciones' => null,
                    'created_at' => $fechaEmision,
                    'updated_at' => $fechaEmision,
                ]);

                foreach ($itemsData as $idx => $it) {
                    VentaItem::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $it['producto']->id,
                        'orden' => $idx + 1,
                        'unidad_medida' => $it['producto']->unidad_medida,
                        'codigo_producto' => $it['producto']->codigo,
                        'descripcion' => $it['producto']->nombre,
                        'cantidad' => $it['cantidad'],
                        'precio_unitario' => $it['precio_unitario'],
                        'precio_unitario_con_igv' => $it['precio_unitario_con_igv'],
                        'valor_unitario' => $it['precio_unitario'],
                        'descuento' => 0,
                        'subtotal' => $it['subtotal'],
                        'igv_item' => $it['igv_item'],
                        'total_item' => $it['total_item'],
                        'tipo_afectacion_igv' => '10',
                    ]);
                }

                $metodoPago = $this->ponderarMetodo($metodos);
                $montoRecibido = $metodoPago === 'efectivo' ? $total + random_int(0, 10) : null;
                $vuelto = $metodoPago === 'efectivo' && $montoRecibido ? max(0, $montoRecibido - $total) : 0;

                Pago::create([
                    'venta_id' => $venta->id,
                    'metodo_pago' => $metodoPago,
                    'monto' => $total,
                    'vuelto' => $vuelto,
                    'monto_recibido' => $montoRecibido,
                    'tipo_tarjeta' => $metodoPago === 'tarjeta' ? 'debito' : null,
                    'marca_tarjeta' => null,
                    'numero_operacion' => $metodoPago === 'efectivo' ? null : str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
                    'numero_voucher' => null,
                    'cuenta_destino' => null,
                    'banco' => null,
                    'created_at' => $fechaEmision,
                    'updated_at' => $fechaEmision,
                ]);

                Comprobante::create([
                    'venta_id' => $venta->id,
                    'tipo_comprobante' => $tipoComprobante,
                    'serie' => $serie->serie,
                    'correlativo' => $correlativo,
                    'correlativo_completo' => $correlativoCompleto,
                    'estado' => 'aceptado',
                    'hash_cpe' => str_repeat('A', 40),
                    'codigo_respuesta' => '0',
                    'descripcion_respuesta' => 'Aceptado por SUNAT',
                    'created_at' => $fechaEmision,
                    'updated_at' => $fechaEmision,
                ]);
            }
        }

        Serie::where('id', $serieBoleta?->id)->update(['correlativo_actual' => $correlativoBoleta - 1]);
        Serie::where('id', $serieFactura?->id)->update(['correlativo_actual' => $correlativoFactura - 1]);
    }

    private function horaPicoCafeteria(): int
    {
        $rand = random_int(1, 100);
        return match (true) {
            $rand <= 5 => 6, $rand <= 15 => 7, $rand <= 35 => 8, $rand <= 50 => 9, $rand <= 60 => 10,
            $rand <= 70 => 11, $rand <= 78 => 12, $rand <= 84 => 13, $rand <= 90 => 14,
            $rand <= 94 => 15, $rand <= 97 => 16, $rand <= 99 => 17, default => 18,
        };
    }

    private function ponderarMetodo(array $metodos): string
    {
        $rand = random_int(1, 100);
        $acum = 0;
        foreach ($metodos as $metodo => $peso) {
            $acum += $peso;
            if ($rand <= $acum) {
                return $metodo;
            }
        }
        return 'efectivo';
    }
}
