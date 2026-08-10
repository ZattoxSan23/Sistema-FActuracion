<?php

namespace App\Http\Controllers;

use App\Models\ArqueoDetalle;
use App\Models\Caja;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArqueoController extends Controller
{
    public function create(Caja $caja)
    {
        if ($caja->estado !== 'abierta') {
            return redirect()->route('caja.index')->with('error', 'La caja no está abierta');
        }
        $denominaciones = ArqueoDetalle::DENOMINACIONES_VALORES;
        return view('arqueo.create', compact('caja', 'denominaciones'));
    }

    public function store(Request $request, Caja $caja)
    {
        $denominaciones = ArqueoDetalle::DENOMINACIONES_VALORES;
        $reglas = [];
        foreach ($denominaciones as $d) {
            $reglas["cantidad.{$d}"] = 'nullable|integer|min:0';
        }

        $data = $request->validate($reglas);
        $cantidades = $data['cantidad'] ?? [];

        DB::transaction(function () use ($caja, $cantidades, $denominaciones, $request) {
            foreach ($denominaciones as $d) {
                $cant = (int) ($cantidades[(string) $d] ?? 0);
                if ($cant <= 0) continue;
                ArqueoDetalle::create([
                    'caja_id' => $caja->id,
                    'user_id' => $request->user()->id,
                    'denominacion' => $d,
                    'cantidad' => $cant,
                    'subtotal' => round($d * $cant, 2),
                    'fecha' => now(),
                ]);
            }
        });

        $caja->recalcularTotales();

        return redirect()->route('arqueo.pdf', $caja->id)
            ->with('success', 'Arqueo registrado');
    }

    public function imprimir(Request $request, $cajaId)
    {
        $caja = Caja::with(['usuarioApertura', 'usuarioCierre', 'arqueoDetalles.usuario'])->findOrFail($cajaId);
        $detalles = $caja->arqueoDetalles;
        $totalContado = $detalles->sum('subtotal');
        $empresa = Empresa::actual();
        $pdf = \PDF::loadView('arqueo.reporte-pdf', compact('caja', 'detalles', 'totalContado', 'empresa'));
        return $pdf->stream("arqueo-caja-{$caja->id}.pdf");
    }
}
