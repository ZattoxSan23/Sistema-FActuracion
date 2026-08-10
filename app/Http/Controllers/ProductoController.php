<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        $productos = $query->orderBy('nombre')->paginate(20);
        $categorias = Categoria::activos()->ordenados()->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::activos()->ordenados()->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $this->validarProducto($request);
        $data['activo'] = true;
        $data['visible_pos'] = $request->boolean('visible_pos', true);

        if (empty($data['codigo'])) {
            $data['codigo'] = $this->generarCodigoDesdeNombre($data['nombre']);
        }
        if (empty($data['codigo_barra'])) {
            $data['codigo_barra'] = $this->generarCodigoBarrasUnico();
        }

        Producto::create($data);

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::activos()->ordenados()->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $this->validarProducto($request, $producto->id);
        $data['activo'] = $request->boolean('activo', true);
        $data['visible_pos'] = $request->boolean('visible_pos', true);

        $producto->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado');
    }

    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');
        $productos = Producto::visiblesPos()
            ->buscar($termino)
            ->limit(20)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'text' => "{$p->codigo} - {$p->nombre} (S/ {$p->precio_venta})",
                'precio' => (float) $p->precio_venta,
            ]);

        return response()->json(['results' => $productos]);
    }

    private function validarProducto(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'categoria_id' => 'nullable|exists:categorias,id',
            'codigo' => 'nullable|string|max:50|unique:productos,codigo' . ($id ? ",{$id}" : ''),
            'codigo_barra' => 'nullable|string|max:50|unique:productos,codigo_barra' . ($id ? ",{$id}" : ''),
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:10',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_mayorista' => 'nullable|numeric|min:0',
            'tipo_afectacion_igv' => 'required|in:10,20,30,40',
            'incluye_igv' => 'boolean',
            'orden' => 'nullable|integer',
        ]);
    }

    private function generarCodigoDesdeNombre(string $nombre): string
    {
        $base = strtoupper(trim($nombre));
        $base = $this->quitarTildes($base);
        $base = preg_replace('/[^A-Z0-9]+/', ' ', $base) ?? '';
        $palabras = array_values(array_filter(explode(' ', $base)));

        if (count($palabras) === 1) {
            $palabra = $palabras[0];
            $prefijo = strlen($palabra) >= 3 ? strtoupper(substr($palabra, 0, 3)) : strtoupper($palabra);
        } else {
            $iniciales = '';
            foreach ($palabras as $palabra) {
                $iniciales .= substr($palabra, 0, 1);
                if (strlen($iniciales) === 3) {
                    break;
                }
            }
            $prefijo = $iniciales;
        }

        $prefijo = substr(str_pad($prefijo, 3, 'X'), 0, 3);

        $tentativo = $prefijo;
        $contador = 1;
        while (Producto::withTrashed()->where('codigo', $tentativo)->exists()) {
            $contador++;
            $tentativo = $prefijo.str_pad((string) $contador, 3, '0', STR_PAD_LEFT);
        }

        return $tentativo;
    }

    private function generarCodigoBarrasUnico(): string
    {
        do {
            $codigo = '200'.str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Producto::withTrashed()->where('codigo_barra', $codigo)->exists());

        return $codigo;
    }

    private function quitarTildes(string $texto): string
    {
        $originales = ['Á','É','Í','Ó','Ú','Ñ','á','é','í','ó','ú','ñ'];
        $reemplazos = ['A','E','I','O','U','N','a','e','i','o','u','n'];
        return str_replace($originales, $reemplazos, $texto);
    }
}
