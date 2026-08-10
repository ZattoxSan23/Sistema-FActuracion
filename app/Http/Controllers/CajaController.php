<?php

namespace App\Http\Controllers;

use App\Exports\CajaMovimientosExport;
use App\Models\Caja;
use App\Models\CajaMovimiento;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $cajaAbierta = Caja::cajaAbierta();

        $query = Caja::with(['usuarioApertura', 'usuarioCierre', 'arqueoDetalles'])
            ->orderByDesc('fecha_apertura');

        if ($request->filled('desde')) {
            $query->whereDate('fecha_apertura', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_apertura', '<=', $request->hasta);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id_apertura', $request->user_id);
        }

        $cajas = $query->paginate(20)->withQueryString();
        $usuarios = User::orderBy('name')->get();

        $kpis = [
            'total' => Caja::count(),
            'abiertas' => Caja::where('estado', 'abierta')->count(),
            'cerradas' => Caja::where('estado', 'cerrada')->count(),
            'diferencia_total' => Caja::where('estado', 'cerrada')->sum('diferencia'),
        ];

        return view('caja.index', compact('cajas', 'cajaAbierta', 'usuarios', 'kpis'));
    }

    public function apertura()
    {
        if (Caja::cajaAbierta()) {
            return redirect()->route('pos.index');
        }

        return view('caja.apertura');
    }

    public function storeApertura(Request $request)
    {
        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        if (Caja::cajaAbierta()) {
            return redirect()->route('pos.index');
        }

        Caja::create([
            'user_id_apertura' => $request->user()->id,
            'fecha_apertura' => now(),
            'monto_apertura' => $request->monto_apertura,
            'estado' => 'abierta',
            'observaciones_apertura' => $request->observaciones,
        ]);

        return redirect()->route('pos.index')
            ->with('success', 'Caja aperturada correctamente');
    }

    public function cierre(Caja $caja)
    {
        if (!$caja->estaAbierta()) {
            return redirect()->route('caja.index');
        }

        $caja->recalcularTotales();
        $caja->load(['usuarioApertura', 'movimientos']);

        return view('caja.cierre', compact('caja'));
    }

    public function storeCierre(Request $request, Caja $caja)
    {
        if (!$caja->estaAbierta()) {
            return back()->with('error', 'La caja ya está cerrada');
        }

        $request->validate([
            'monto_efectivo_real' => 'required|numeric|min:0',
            'observaciones_cierre' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $caja) {
            $caja->recalcularTotales();
            $caja->update([
                'user_id_cierre' => $request->user()->id,
                'fecha_cierre' => now(),
                'monto_efectivo_real' => $request->monto_efectivo_real,
                'diferencia' => round((float) $request->monto_efectivo_real - (float) $caja->monto_efectivo_teorico, 2),
                'estado' => 'cerrada',
                'observaciones_cierre' => $request->observaciones_cierre,
            ]);
        });

        return redirect()->route('caja.index')
            ->with('success', 'Caja cerrada correctamente');
    }

    public function movimientos(Request $request, Caja $caja)
    {
        $query = $caja->movimientos()->with(['usuario', 'venta'])->orderByDesc('fecha');
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }
        $movimientos = $query->paginate(50)->withQueryString();

        $caja->load(['usuarioApertura', 'usuarioCierre']);
        return view('caja.movimientos', compact('caja', 'movimientos'));
    }

    public function movimientoRapido(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:200',
            'metodo_pago' => 'required|in:efectivo,tarjeta,yape,plin,transferencia,otros',
        ]);

        $caja = Caja::cajaAbierta();
        if (!$caja) {
            return back()->with('error', 'No hay caja abierta');
        }

        CajaMovimiento::create([
            'caja_id' => $caja->id,
            'user_id' => $request->user()->id,
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'concepto' => $request->concepto,
            'referencia' => $request->referencia,
            'fecha' => now(),
        ]);

        $caja->recalcularTotales();

        return back()->with('success', 'Movimiento registrado');
    }

    public function reportePdf(Caja $caja)
    {
        $caja->load(['usuarioApertura', 'usuarioCierre', 'movimientos.usuario', 'arqueoDetalles', 'ventas.cliente', 'ventas.pagos']);
        $empresa = Empresa::actual();
        $pdf = \PDF::loadView('caja.reporte-pdf', compact('caja', 'empresa'));
        return $pdf->stream("cierre-caja-{$caja->id}.pdf");
    }

    public function exportExcel(Caja $caja)
    {
        return Excel::download(new CajaMovimientosExport($caja), "caja-{$caja->id}-movimientos.xlsx");
    }
}
