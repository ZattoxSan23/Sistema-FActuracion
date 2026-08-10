<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        $clientes = $query->orderBy('nombre_razon_social')->paginate(20);

        return view('clientes.index', compact('clientes'));
    }

    public function create(Request $request)
    {
        $prefill = [
            'tipo_documento' => old('tipo_documento', $request->input('prefill_tipo_documento', 'DNI')),
            'numero_documento' => old('numero_documento', $request->input('prefill_numero_documento', '')),
            'nombre_razon_social' => old('nombre_razon_social', $request->input('prefill_nombre_razon_social', '')),
            'direccion' => old('direccion', $request->input('prefill_direccion', '')),
        ];

        return view('clientes.create', compact('prefill'));
    }

    public function store(Request $request)
    {
        $data = $this->validarCliente($request);

        $cliente = Cliente::updateOrCreate(
            [
                'tipo_documento' => $data['tipo_documento'],
                'numero_documento' => $data['numero_documento'],
            ],
            $data
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cliente' => $cliente]);
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente guardado correctamente');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $this->validarCliente($request);
        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado');
    }

    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');
        $clientes = Cliente::activos()
            ->buscar($termino)
            ->limit(20)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'text' => "{$c->documento_completo} - {$c->nombre_razon_social}",
            ]);

        return response()->json(['results' => $clientes]);
    }

    private function validarCliente(Request $request): array
    {
        return $request->validate([
            'tipo_documento' => 'required|in:DNI,RUC,CE,PASAPORTE,SIN_DOCUMENTO',
            'numero_documento' => 'required|string|max:15',
            'nombre_razon_social' => 'required|string|max:200',
            'direccion' => 'nullable|string|max:250',
            'ubigeo' => 'nullable|string|max:6',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:200',
        ]);
    }
}
