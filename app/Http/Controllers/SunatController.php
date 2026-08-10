<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\SunatConfig;
use App\Models\SunatRespuesta;
use App\Services\Sunat\SunatService;
use Illuminate\Http\Request;

class SunatController extends Controller
{
    public function __construct(private SunatService $sunat)
    {
    }

    public function configuracion()
    {
        $config = SunatConfig::actual() ?? new SunatConfig();
        return view('sunat.configuracion', compact('config'));
    }

    public function guardarConfiguracion(Request $request)
    {
        $data = $request->validate([
            'entorno' => 'required|in:beta,produccion',
            'modo_envio' => 'required|in:gre,ose',
            'gre_url' => 'nullable|url',
            'ose_url' => 'nullable|url',
            'usuario_sol' => 'required|string|max:50',
            'clave_sol' => 'required|string|max:100',
            'certificado_password' => 'nullable|string|max:200',
            'envio_automatico' => 'boolean',
            'intentos_max' => 'required|integer|min:1|max:10',
            'timeout_segundos' => 'required|integer|min:10|max:300',
            'notas' => 'nullable|string',
        ]);

        $data['envio_automatico'] = $request->boolean('envio_automatico');

        $config = SunatConfig::actual();
        if ($config) {
            $config->update($data);
        } else {
            $config = SunatConfig::create($data);
        }

        return back()->with('success', 'Configuración guardada correctamente');
    }

    public function probarConexion(Request $request)
    {
        try {
            $config = SunatConfig::actual();
            if (!$config) {
                return response()->json(['success' => false, 'error' => 'No hay configuración']);
            }

            $url = $config->modo_envio === 'ose' ? $config->ose_url : $config->gre_url;

            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            $response = $client->get($url, ['http_errors' => false]);

            return response()->json([
                'success' => $response->getStatusCode() < 500,
                'http_status' => $response->getStatusCode(),
                'message' => $response->getStatusCode() < 500
                    ? 'Conexión exitosa al servidor'
                    : 'Servidor no disponible',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function comprobantes(Request $request)
    {
        $query = Comprobante::with(['venta.cliente', 'venta.usuario']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_comprobante', $request->tipo);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_emision', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->hasta);
        }

        $comprobantes = $query->orderByDesc('fecha_emision')->paginate(20)->withQueryString();

        return view('sunat.comprobantes', compact('comprobantes'));
    }

    public function verComprobante(Comprobante $comprobante)
    {
        $comprobante->load(['venta.cliente', 'venta.items', 'venta.pagos']);
        return view('sunat.detalle', compact('comprobante'));
    }

    public function reenviar(Comprobante $comprobante)
    {
        try {
            $this->sunat->reenviar($comprobante);
            return back()->with('success', 'Comprobante reenviado');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function respuestas(Request $request)
    {
        $query = SunatRespuesta::with('comprobante')->orderByDesc('created_at');

        if ($request->filled('tipo')) {
            $query->where('tipo_operacion', $request->tipo);
        }
        if ($request->filled('exito')) {
            $query->where('exito', $request->exito === '1');
        }

        $respuestas = $query->paginate(30);

        return view('sunat.respuestas', compact('respuestas'));
    }
}
