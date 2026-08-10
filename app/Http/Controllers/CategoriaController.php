<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::ordenados()->withCount('productos')->get();
        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer',
        ]);

        Categoria::create($data);

        return back()->with('success', 'Categoría creada');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $categoria->update($data);

        return back()->with('success', 'Categoría actualizada');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return back()->with('success', 'Categoría eliminada');
    }
}
