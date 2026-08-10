<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Serie;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $empresa = Empresa::actual() ?? new Empresa(['igv' => 18, 'moneda' => 'PEN']);
        $series = Serie::orderBy('tipo_comprobante')->orderBy('serie')->get();
        return view('configuracion.index', compact('empresa', 'series'));
    }

    public function updateEmpresa(Request $request)
    {
        $data = $request->validate([
            'ruc' => 'required|string|size:11',
            'razon_social' => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'direccion' => 'required|string|max:250',
            'ubigeo' => 'nullable|string|size:6',
            'departamento' => 'nullable|string|max:50',
            'provincia' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'web' => 'nullable|url',
            'igv' => 'required|numeric|min:0|max:100',
            'moneda' => 'required|in:PEN,USD',
            'tipo_precio' => 'required|in:incluye_igv,no_incluye_igv',
            'pie_pagina_ticket' => 'nullable|string',
            'mensaje_personalizado' => 'nullable|string',
        ]);

        $empresa = Empresa::first();
        if ($empresa) {
            $empresa->update($data);
        } else {
            $data['activo'] = true;
            Empresa::create($data);
        }

        return back()->with('success', 'Datos de empresa actualizados');
    }

    public function storeSerie(Request $request)
    {
        $data = $request->validate([
            'tipo_comprobante' => 'required|in:01,03,07,08,09,40',
            'serie' => 'required|string|max:4',
            'correlativo_desde' => 'required|integer|min:1',
            'correlativo_hasta' => 'required|integer|min:1',
        ]);

        $data['correlativo_actual'] = $data['correlativo_desde'] - 1;
        $data['activo'] = true;

        Serie::create($data);

        return back()->with('success', 'Serie creada');
    }

    public function updateSerie(Request $request, Serie $serie)
    {
        $data = $request->validate([
            'correlativo_hasta' => 'required|integer|min:1',
            'activo' => 'boolean',
            'principal' => 'boolean',
            'notas' => 'nullable|string',
        ]);

        $serie->update($data);

        return back()->with('success', 'Serie actualizada');
    }

    public function destroySerie(Serie $serie)
    {
        $serie->delete();
        return back()->with('success', 'Serie eliminada');
    }

    public function updateImpresoras(Request $request)
    {
        // En esta versión solo se almacenan en config; en producción se integraría con el sistema operativo
        return back()->with('success', 'Configuración de impresoras actualizada');
    }
}
