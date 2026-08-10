<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Services\Sunat\SunatService;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function __construct(private SunatService $sunat)
    {
    }

    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'comprobante']);

        if ($request->filled('desde')) {
            $query->whereDate('fecha_emision', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->hasta);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_comprobante', $request->tipo);
        }
        if ($request->filled('estado_sunat')) {
            $query->where('estado_sunat', $request->estado_sunat);
        }
        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->where(function ($q) use ($termino) {
                $q->where('correlativo', 'ILIKE', "%{$termino}%")
                  ->orWhereHas('cliente', fn ($c) => $c->where('nombre_razon_social', 'ILIKE', "%{$termino}%")
                      ->orWhere('numero_documento', 'ILIKE', "%{$termino}%"));
            });
        }

        $ventas = (clone $query)->orderByDesc('fecha_emision')->paginate(20)->withQueryString();

        $totales = (clone $query)->where('estado', '!=', 'anulada')
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as cantidad')
            ->first();

        return view('ventas.index', compact('ventas', 'totales'));
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'items.producto', 'pagos', 'comprobante', 'caja']);
        return view('ventas.show', compact('venta'));
    }

    public function anular(Request $request, Venta $venta)
    {
        if ($venta->estado === 'anulada') {
            return back()->with('error', 'La venta ya está anulada');
        }

        $request->validate([
            'motivo_anulacion' => 'required|string|min:5|max:500',
        ]);

        $venta->update([
            'estado' => 'anulada',
            'estado_sunat' => 'anulado',
            'motivo_anulacion' => $request->motivo_anulacion,
            'user_id_anulacion' => $request->user()->id,
            'fecha_anulacion' => now(),
        ]);

        // Si tenía comprobante aceptado, generar comunicación de baja
        if ($venta->comprobante && $venta->comprobante->isAceptado()) {
            try {
                $this->sunat->comunicacionBaja($venta);
            } catch (\Exception $e) {
                \Log::warning('No se pudo generar comunicación de baja: ' . $e->getMessage());
            }
        }

        // Actualizar totales de caja si está abierta
        if ($venta->caja_id) {
            $venta->caja?->recalcularTotales();
        }

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta anulada correctamente');
    }

    public function reenviarSunat(Venta $venta)
    {
        if (!$venta->comprobante) {
            return back()->with('error', 'Esta venta no tiene comprobante electrónico');
        }

        try {
            $this->sunat->reenviar($venta->comprobante);
            return back()->with('success', 'Comprobante reenviado a SUNAT');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al reenviar: ' . $e->getMessage());
        }
    }

    public function pdf(Venta $venta)
    {
        $venta->load(['cliente', 'items', 'pagos', 'comprobante', 'usuario']);
        $empresa = \App\Models\Empresa::actual();
        $pdf = \PDF::loadView('pos.pdf-a4', compact('venta', 'empresa'));
        return $pdf->stream("venta-{$venta->correlativo}.pdf");
    }

    public function xml(Venta $venta)
    {
        if (!$venta->comprobante || !$venta->comprobante->xml_firmado) {
            abort(404, 'No hay XML firmado');
        }
        return response($venta->comprobante->xml_firmado)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', "attachment; filename={$venta->correlativo}.xml");
    }
}
