<?php

namespace App\Http\Controllers;

use App\Services\ConsultaDocumentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConsultaDocumentoController extends Controller
{
    public function __construct(private ConsultaDocumentoService $consulta)
    {
    }

    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo' => 'required|in:DNI,RUC',
            'numero' => 'required|string|min:8|max:15',
        ]);

        try {
            $data = $validated['tipo'] === 'DNI'
                ? $this->consulta->consultarDni($validated['numero'])
                : $this->consulta->consultarRuc($validated['numero']);
        } catch (\Throwable $e) {
            Log::info('Consulta Decolecta sin resultados', [
                'tipo' => $validated['tipo'],
                'numero' => $validated['numero'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

        return response()->json([
            'success' => true,
            'cliente' => $data,
        ]);
    }
}
