<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $data = [];

        if ($user->isAdmin() || $user->isContador()) {
            $data = $this->dashboardCompleto();
        } elseif ($user->isCajera()) {
            $data = $this->dashboardCajera($user);
        }

        return view('dashboard.index', compact('data'));
    }

    private function dashboardCompleto(): array
    {
        $hoy = today();
        $inicioMes = now()->startOfMonth();

        $ventasHoy = Venta::delDia()->emitidas()->sum('total');
        $ventasMes = Venta::whereBetween('fecha_emision', [$inicioMes, now()])->emitidas()->sum('total');
        $cantidadVentasHoy = Venta::delDia()->emitidas()->count();
        $cantidadVentasMes = Venta::whereBetween('fecha_emision', [$inicioMes, now()])->emitidas()->count();

        $ticketPromedio = $cantidadVentasHoy > 0 ? $ventasHoy / $cantidadVentasHoy : 0;

        // Ventas por día (últimos 7 días)
        $ventasPorDia = Venta::query()
            ->selectRaw("DATE(fecha_emision) as fecha, COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total")
            ->where('fecha_emision', '>=', now()->subDays(7))
            ->where('estado', '!=', 'anulada')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Top productos del mes
        $topProductos = DB::table('venta_items')
            ->join('ventas', 'venta_items.venta_id', '=', 'ventas.id')
            ->join('productos', 'venta_items.producto_id', '=', 'productos.id')
            ->select(
                'productos.nombre',
                'productos.codigo',
                DB::raw('SUM(venta_items.cantidad) as cantidad_vendida'),
                DB::raw('SUM(venta_items.total_item) as total_vendido')
            )
            ->whereBetween('ventas.fecha_emision', [$inicioMes, now()])
            ->where('ventas.estado', '!=', 'anulada')
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
            ->orderByDesc('cantidad_vendida')
            ->limit(10)
            ->get();

        // Ventas por tipo de comprobante
        $ventasPorTipo = Venta::query()
            ->selectRaw('tipo_comprobante, COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total')
            ->where('fecha_emision', '>=', $inicioMes)
            ->where('estado', '!=', 'anulada')
            ->groupBy('tipo_comprobante')
            ->get();

        // Comprobantes pendientes SUNAT
        $pendientesSunat = Venta::whereIn('estado_sunat', ['pendiente', 'excepcion'])->count();

        return [
            'tipo' => 'completo',
            'ventas_hoy' => $ventasHoy,
            'ventas_mes' => $ventasMes,
            'cantidad_ventas_hoy' => $cantidadVentasHoy,
            'cantidad_ventas_mes' => $cantidadVentasMes,
            'ticket_promedio' => $ticketPromedio,
            'total_clientes' => Cliente::count(),
            'total_productos' => Producto::activos()->count(),
            'ventas_por_dia' => $ventasPorDia,
            'top_productos' => $topProductos,
            'ventas_por_tipo' => $ventasPorTipo,
            'pendientes_sunat' => $pendientesSunat,
            'ultimas_ventas' => Venta::with(['cliente', 'usuario'])->latest('fecha_emision')->limit(10)->get(),
        ];
    }

    private function dashboardCajera($user): array
    {
        $caja = $user->cajaAbierta();

        $ventasHoy = Venta::where('user_id', $user->id)
            ->delDia()
            ->emitidas()
            ->sum('total');

        $ventasUsuario = Venta::where('user_id', $user->id)
            ->emitidas()
            ->count();

        $ventasRecientes = Venta::with('cliente')
            ->where('user_id', $user->id)
            ->delDia()
            ->latest('fecha_emision')
            ->limit(20)
            ->get();

        return [
            'tipo' => 'cajera',
            'caja' => $caja,
            'ventas_hoy' => $ventasHoy,
            'ventas_usuario' => $ventasUsuario,
            'ventas_recientes' => $ventasRecientes,
            'productos_disponibles' => Producto::visiblesPos()->count(),
        ];
    }
}
