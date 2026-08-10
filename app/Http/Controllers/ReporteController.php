<?php

namespace App\Http\Controllers;

use App\Exports\ReporteEstadoCuentaClienteExport;
use App\Exports\ReporteFlujoCajaExport;
use App\Exports\ReporteResumenDiarioExport;
use App\Exports\ReporteVentasExport;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index()
    {
        $kpis = $this->getKpis();
        return view('reportes.index', compact('kpis'));
    }

    public function ventas(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = $this->queryVentas($filtros);
        $totales = $this->totalesVentas($ventas);
        $usuarios = User::orderBy('name')->get();

        return view('reportes.ventas', compact('ventas', 'totales', 'filtros', 'usuarios'));
    }

    public function ventasExcel(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = $this->queryVentas($filtros);
        $totales = $this->totalesVentas($ventas);
        return Excel::download(new ReporteVentasExport($ventas, $totales, $filtros), "reporte-ventas-{$filtros['desde']}-{$filtros['hasta']}.xlsx");
    }

    public function ventasPdf(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = $this->queryVentas($filtros);
        $totales = $this->totalesVentas($ventas);
        $empresa = Empresa::actual();
        $usuarios = User::orderBy('name')->get();

        $pdf = \PDF::loadView('reportes.pdf.ventas', compact('ventas', 'totales', 'filtros', 'empresa', 'usuarios'));
        return $pdf->stream("reporte-ventas-{$filtros['desde']}-{$filtros['hasta']}.pdf");
    }

    public function libroVentas(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = $this->queryVentas($filtros);
        $usuarios = User::orderBy('name')->get();
        return view('reportes.libro-ventas', compact('ventas', 'filtros', 'usuarios'));
    }

    public function libroVentasPdf(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = $this->queryVentas($filtros);
        $empresa = Empresa::actual();
        $totales = $this->totalesVentas($ventas);

        $pdf = \PDF::loadView('reportes.pdf.libro-ventas', compact('ventas', 'filtros', 'empresa', 'totales'));
        return $pdf->stream("libro-ventas-{$filtros['desde']}-{$filtros['hasta']}.pdf");
    }

    public function productosMasVendidos(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $query = DB::table('venta_items')
            ->join('ventas', 'venta_items.venta_id', '=', 'ventas.id')
            ->join('productos', 'venta_items.producto_id', '=', 'productos.id')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select(
                'productos.id', 'productos.nombre', 'productos.codigo',
                'categorias.nombre as categoria',
                DB::raw('SUM(venta_items.cantidad) as cantidad_vendida'),
                DB::raw('SUM(venta_items.total_item) as total_vendido'),
                DB::raw('COUNT(DISTINCT ventas.id) as veces_vendido'),
            )
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo', 'categorias.nombre')
            ->orderByDesc('cantidad_vendida')
            ->limit(50);

        if (!empty($filtros['user_id'])) {
            $query->where('ventas.user_id', $filtros['user_id']);
        }
        if (!empty($filtros['categoria_id'])) {
            $query->where('productos.categoria_id', $filtros['categoria_id']);
        }

        $productos = $query->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('reportes.productos-mas-vendidos', compact('productos', 'filtros', 'categorias', 'usuarios'));
    }

    public function porCategoria(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $query = DB::table('venta_items')
            ->join('ventas', 'venta_items.venta_id', '=', 'ventas.id')
            ->join('productos', 'venta_items.producto_id', '=', 'productos.id')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select(
                'categorias.id', 'categorias.nombre',
                DB::raw('SUM(venta_items.cantidad) as cantidad_vendida'),
                DB::raw('SUM(venta_items.total_item) as total_vendido'),
            )
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total_vendido');

        if (!empty($filtros['user_id'])) {
            $query->where('ventas.user_id', $filtros['user_id']);
        }

        $categorias = $query->get();
        $totalGeneral = $categorias->sum('total_vendido');
        $usuarios = User::orderBy('name')->get();

        return view('reportes.por-categoria', compact('categorias', 'totalGeneral', 'filtros', 'usuarios'));
    }

    public function porVendedor(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $query = DB::table('ventas')
            ->join('users', 'ventas.user_id', '=', 'users.id')
            ->select(
                'users.id', 'users.name', 'users.email', 'users.rol',
                DB::raw('COUNT(ventas.id) as cantidad_ventas'),
                DB::raw('COALESCE(SUM(ventas.total), 0) as total_vendido'),
            )
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.rol')
            ->orderByDesc('total_vendido');

        if (!empty($filtros['user_id'])) {
            $query->where('ventas.user_id', $filtros['user_id']);
        }

        $vendedores = $query->get();

        return view('reportes.por-vendedor', compact('vendedores', 'filtros'));
    }

    public function porMetodoPago(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $query = DB::table('pagos')
            ->join('ventas', 'pagos.venta_id', '=', 'ventas.id')
            ->select(
                'pagos.metodo_pago',
                DB::raw('COUNT(pagos.id) as cantidad'),
                DB::raw('SUM(pagos.monto) as total'),
                DB::raw('SUM(pagos.vuelto) as total_vuelto'),
            )
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('pagos.metodo_pago')
            ->orderByDesc('total');

        if (!empty($filtros['user_id'])) {
            $query->where('ventas.user_id', $filtros['user_id']);
        }

        $metodos = $query->get();
        $usuarios = User::orderBy('name')->get();

        return view('reportes.por-metodo-pago', compact('metodos', 'filtros', 'usuarios'));
    }

    public function diario(Request $request)
    {
        $fecha = $request->get('fecha', today()->format('Y-m-d'));
        $ventas = Venta::with(['cliente', 'pagos'])
            ->whereDate('fecha_emision', $fecha)
            ->where('estado', '!=', 'anulada')
            ->orderBy('fecha_emision')
            ->get();

        $totales = [
            'cantidad' => $ventas->count(),
            'total' => $ventas->sum('total'),
            'igv' => $ventas->sum('igv'),
            'boletas' => $ventas->where('tipo_comprobante', '03')->count(),
            'facturas' => $ventas->where('tipo_comprobante', '01')->count(),
        ];
        $porHora = $ventas->groupBy(fn ($v) => $v->fecha_emision->format('H'))
            ->map(fn ($g) => ['cantidad' => $g->count(), 'total' => $g->sum('total')]);

        return view('reportes.diario', compact('ventas', 'totales', 'porHora', 'fecha'));
    }

    public function mensual(Request $request)
    {
        $año = $request->get('año', now()->year);
        $ventas = Venta::whereYear('fecha_emision', $año)->where('estado', '!=', 'anulada')->get();
        $porMes = $ventas->groupBy(fn ($v) => $v->fecha_emision->format('n'))
            ->map(fn ($g, $m) => ['mes' => $m, 'cantidad' => $g->count(), 'total' => $g->sum('total'), 'igv' => $g->sum('igv')]);
        return view('reportes.mensual', compact('porMes', 'año'));
    }

    public function igv(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $query = Venta::whereBetween('fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('estado', '!=', 'anulada');
        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }
        $ventas = $query->get();
        $igv = [
            'base_imponible' => $ventas->sum('op_gravadas'),
            'igv' => $ventas->sum('igv'),
            'total' => $ventas->sum('total'),
            'exoneradas' => $ventas->sum('op_exoneradas'),
            'inafectas' => $ventas->sum('op_inafectas'),
        ];
        $usuarios = User::orderBy('name')->get();
        return view('reportes.igv', compact('igv', 'filtros', 'usuarios'));
    }

    public function stockCritico(Request $request)
    {
        $umbral = (float) $request->get('umbral', 10);
        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->orderBy('stock')
            ->get()
            ->map(function ($p) use ($umbral) {
                $p->stock_actual = (float) $p->stock;
                $p->estado_stock = $p->stock_actual <= 0 ? 'sin_stock' : ($p->stock_actual <= $umbral ? 'critico' : 'bajo');
                return $p;
            })
            ->filter(fn ($p) => $p->estado_stock !== 'bajo')
            ->values();
        return view('reportes.stock-critico', compact('productos', 'umbral'));
    }

    public function topClientes(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $clientes = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->select(
                'clientes.id', 'clientes.tipo_documento', 'clientes.numero_documento', 'clientes.nombre_razon_social',
                DB::raw('COUNT(ventas.id) as cantidad_compras'),
                DB::raw('COALESCE(SUM(ventas.total), 0) as total_comprado'),
                DB::raw('MAX(ventas.fecha_emision) as ultima_compra'),
            )
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('clientes.id', 'clientes.tipo_documento', 'clientes.numero_documento', 'clientes.nombre_razon_social')
            ->orderByDesc('total_comprado')
            ->limit(30)
            ->get();
        return view('reportes.top-clientes', compact('clientes', 'filtros'));
    }

    public function margenGanancia(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $datos = DB::table('venta_items')
            ->join('ventas', 'venta_items.venta_id', '=', 'ventas.id')
            ->join('productos', 'venta_items.producto_id', '=', 'productos.id')
            ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select(
                'productos.id', 'productos.codigo', 'productos.nombre',
                'categorias.nombre as categoria',
                DB::raw('SUM(venta_items.cantidad) as cantidad'),
                DB::raw('SUM(venta_items.subtotal) as ingreso_neto'),
                DB::raw('SUM(venta_items.cantidad * productos.precio_compra) as costo'),
            )
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('productos.id', 'productos.codigo', 'productos.nombre', 'categorias.nombre')
            ->get()
            ->map(function ($d) {
                $d->utilidad = (float) $d->ingreso_neto - (float) $d->costo;
                $d->margen_pct = $d->ingreso_neto > 0 ? round(($d->utilidad / (float) $d->ingreso_neto) * 100, 1) : 0;
                return $d;
            })
            ->sortByDesc('utilidad');
        return view('reportes.margen-ganancia', compact('datos', 'filtros'));
    }

    public function flujoCaja(Request $request)
    {
        $filtros = $this->filtrosBase($request);

        $ingresos = DB::table('pagos')
            ->join('ventas', 'pagos.venta_id', '=', 'ventas.id')
            ->select(DB::raw("to_char(ventas.fecha_emision, 'YYYY-MM-DD') as fecha"), DB::raw('SUM(pagos.monto) as total'))
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('fecha')
            ->orderBy('fecha');
        if (!empty($filtros['user_id'])) {
            $ingresos->where('ventas.user_id', $filtros['user_id']);
        }
        $ingresos = $ingresos->pluck('total', 'fecha');

        $egresos = DB::table('caja_movimientos')
            ->select(DB::raw("to_char(fecha, 'YYYY-MM-DD') as fecha"), DB::raw('SUM(monto) as total'))
            ->where('tipo', 'egreso')
            ->whereBetween('fecha', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        $fechas = array_unique(array_merge(array_keys($ingresos->toArray()), array_keys($egresos->toArray())));
        sort($fechas);

        $datos = [];
        $totalIngresos = 0;
        $totalEgresos = 0;
        foreach ($fechas as $f) {
            $ing = (float) ($ingresos[$f] ?? 0);
            $egr = (float) ($egresos[$f] ?? 0);
            $datos[] = ['fecha' => $f, 'ingresos' => $ing, 'egresos' => $egr, 'neto' => $ing - $egr];
            $totalIngresos += $ing;
            $totalEgresos += $egr;
        }

        $totales = ['ingresos' => $totalIngresos, 'egresos' => $totalEgresos, 'neto' => $totalIngresos - $totalEgresos];
        $usuarios = User::orderBy('name')->get();
        return view('reportes.flujo-caja', compact('datos', 'totales', 'filtros', 'usuarios'));
    }

    public function flujoCajaExcel(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $datos = $this->calcularFlujoCaja($filtros);
        $totales = ['ingresos' => array_sum(array_column($datos, 'ingresos')), 'egresos' => array_sum(array_column($datos, 'egresos')), 'neto' => 0];
        $totales['neto'] = $totales['ingresos'] - $totales['egresos'];
        return Excel::download(new ReporteFlujoCajaExport($datos, $totales, $filtros), "flujo-caja-{$filtros['desde']}-{$filtros['hasta']}.xlsx");
    }

    public function flujoCajaPdf(Request $request)
    {
        $filtros = $this->filtrosBase($request);
        $datos = $this->calcularFlujoCaja($filtros);
        $totales = ['ingresos' => array_sum(array_column($datos, 'ingresos')), 'egresos' => array_sum(array_column($datos, 'egresos')), 'neto' => 0];
        $totales['neto'] = $totales['ingresos'] - $totales['egresos'];
        $empresa = Empresa::actual();
        $pdf = \PDF::loadView('reportes.pdf.flujo-caja', compact('datos', 'totales', 'filtros', 'empresa'));
        return $pdf->stream("flujo-caja-{$filtros['desde']}-{$filtros['hasta']}.pdf");
    }

    public function resumenDiario(Request $request)
    {
        $fecha = $request->get('fecha', today()->format('Y-m-d'));
        $datos = $this->calcularResumenDiario($fecha);
        return view('reportes.resumen-diario', compact('datos', 'fecha'));
    }

    public function resumenDiarioExcel(Request $request)
    {
        $fecha = $request->get('fecha', today()->format('Y-m-d'));
        $datos = $this->calcularResumenDiario($fecha);
        return Excel::download(new ReporteResumenDiarioExport($datos, $fecha), "resumen-diario-{$fecha}.xlsx");
    }

    public function resumenDiarioPdf(Request $request)
    {
        $fecha = $request->get('fecha', today()->format('Y-m-d'));
        $datos = $this->calcularResumenDiario($fecha);
        $empresa = Empresa::actual();
        $pdf = \PDF::loadView('reportes.pdf.resumen-diario', compact('datos', 'fecha', 'empresa'));
        return $pdf->stream("resumen-diario-{$fecha}.pdf");
    }

    public function estadoCuentaCliente(Request $request, Cliente $cliente)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = Venta::with(['items', 'pagos', 'comprobante'])
            ->where('cliente_id', $cliente->id)
            ->whereBetween('fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->orderBy('fecha_emision')
            ->get();
        $totales = $this->totalesVentas($ventas);
        return view('reportes.cliente-cuenta', compact('cliente', 'ventas', 'totales', 'filtros'));
    }

    public function estadoCuentaClientePdf(Request $request, Cliente $cliente)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = Venta::with(['items', 'pagos', 'comprobante'])
            ->where('cliente_id', $cliente->id)
            ->whereBetween('fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->orderBy('fecha_emision')
            ->get();
        $totales = $this->totalesVentas($ventas);
        $empresa = Empresa::actual();
        $pdf = \PDF::loadView('reportes.pdf.cliente-cuenta', compact('cliente', 'ventas', 'totales', 'filtros', 'empresa'));
        return $pdf->stream("cuenta-cliente-{$cliente->id}.pdf");
    }

    public function estadoCuentaClienteExcel(Request $request, Cliente $cliente)
    {
        $filtros = $this->filtrosBase($request);
        $ventas = Venta::with(['items', 'pagos', 'comprobante'])
            ->where('cliente_id', $cliente->id)
            ->whereBetween('fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->orderBy('fecha_emision')
            ->get();
        $totales = $this->totalesVentas($ventas);
        return Excel::download(new ReporteEstadoCuentaClienteExport($cliente, $ventas, $totales, $filtros), "cuenta-cliente-{$cliente->id}.xlsx");
    }

    private function filtrosBase(Request $request): array
    {
        return [
            'desde' => $request->get('desde', now()->startOfMonth()->format('Y-m-d')),
            'hasta' => $request->get('hasta', now()->format('Y-m-d')),
            'user_id' => $request->get('user_id'),
            'categoria_id' => $request->get('categoria_id'),
        ];
    }

    private function queryVentas(array $filtros)
    {
        $query = Venta::with(['cliente', 'usuario', 'comprobante'])
            ->whereBetween('fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('estado', '!=', 'anulada')
            ->orderBy('fecha_emision');
        if (!empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }
        return $query->get();
    }

    private function totalesVentas($ventas): array
    {
        return [
            'cantidad' => $ventas->count(),
            'total' => $ventas->sum('total'),
            'gravadas' => $ventas->sum('op_gravadas'),
            'exoneradas' => $ventas->sum('op_exoneradas'),
            'inafectas' => $ventas->sum('op_inafectas'),
            'igv' => $ventas->sum('igv'),
            'boletas' => $ventas->where('tipo_comprobante', '03')->count(),
            'facturas' => $ventas->where('tipo_comprobante', '01')->count(),
        ];
    }

    private function calcularFlujoCaja(array $filtros): array
    {
        $ingresos = DB::table('pagos')
            ->join('ventas', 'pagos.venta_id', '=', 'ventas.id')
            ->select(DB::raw("to_char(ventas.fecha_emision, 'YYYY-MM-DD') as fecha"), DB::raw('SUM(pagos.monto) as total'))
            ->whereBetween('ventas.fecha_emision', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('fecha')
            ->orderBy('fecha');
        if (!empty($filtros['user_id'])) {
            $ingresos->where('ventas.user_id', $filtros['user_id']);
        }
        $ingresos = $ingresos->pluck('total', 'fecha');

        $egresos = DB::table('caja_movimientos')
            ->select(DB::raw("to_char(fecha, 'YYYY-MM-DD') as fecha"), DB::raw('SUM(monto) as total'))
            ->where('tipo', 'egreso')
            ->whereBetween('fecha', [$filtros['desde'].' 00:00:00', $filtros['hasta'].' 23:59:59'])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        $fechas = array_unique(array_merge(array_keys($ingresos->toArray()), array_keys($egresos->toArray())));
        sort($fechas);

        $datos = [];
        foreach ($fechas as $f) {
            $ing = (float) ($ingresos[$f] ?? 0);
            $egr = (float) ($egresos[$f] ?? 0);
            $datos[] = ['fecha' => $f, 'ingresos' => $ing, 'egresos' => $egr, 'neto' => $ing - $egr];
        }
        return $datos;
    }

    private function calcularResumenDiario(string $fecha): array
    {
        $ventas = Venta::with(['pagos'])
            ->whereDate('fecha_emision', $fecha)
            ->where('estado', '!=', 'anulada')
            ->get();

        $egresosCaja = DB::table('caja_movimientos')
            ->where('tipo', 'egreso')
            ->whereDate('fecha', $fecha)
            ->sum('monto');

        $porMetodo = $ventas->flatMap->pagos
            ->groupBy('metodo_pago')
            ->map(fn ($g) => ['cantidad' => $g->count(), 'total' => $g->sum('monto')]);

        $porHora = $ventas->groupBy(fn ($v) => $v->fecha_emision->format('H'))
            ->map(fn ($g) => ['cantidad' => $g->count(), 'total' => $g->sum('total')]);

        $topProductos = DB::table('venta_items')
            ->join('ventas', 'venta_items.venta_id', '=', 'ventas.id')
            ->join('productos', 'venta_items.producto_id', '=', 'productos.id')
            ->select('productos.nombre', DB::raw('SUM(venta_items.cantidad) as cantidad'), DB::raw('SUM(venta_items.total_item) as total'))
            ->whereDate('ventas.fecha_emision', $fecha)
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(10)
            ->get();

        return [
            'kpis' => [
                'ventas_count' => $ventas->count(),
                'total_ventas' => $ventas->sum('total'),
                'igv' => $ventas->sum('igv'),
                'boletas' => $ventas->where('tipo_comprobante', '03')->count(),
                'facturas' => $ventas->where('tipo_comprobante', '01')->count(),
                'egresos_caja' => (float) $egresosCaja,
                'total_egresos' => (float) $egresosCaja,
                'neto' => (float) $ventas->sum('total') - (float) $egresosCaja,
            ],
            'por_metodo' => $porMetodo,
            'por_hora' => $porHora,
            'top_productos' => $topProductos,
        ];
    }

    private function getKpis(): array
    {
        $hoy = Venta::whereDate('fecha_emision', today())->where('estado', '!=', 'anulada');
        return [
            'ventas_hoy' => (clone $hoy)->count(),
            'monto_hoy' => (clone $hoy)->sum('total'),
            'ventas_mes' => Venta::whereMonth('fecha_emision', now()->month)->whereYear('fecha_emision', now()->year)->where('estado', '!=', 'anulada')->count(),
            'monto_mes' => Venta::whereMonth('fecha_emision', now()->month)->whereYear('fecha_emision', now()->year)->where('estado', '!=', 'anulada')->sum('total'),
            'clientes_activos' => Cliente::where('activo', true)->count(),
            'productos_activos' => Producto::where('activo', true)->count(),
        ];
    }
}
