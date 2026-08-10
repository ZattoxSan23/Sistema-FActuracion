@extends('layouts.app')

@section('title', 'Productos')
@section('header', 'Productos')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-box me-2"></i>Productos</h2>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('productos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nuevo Producto
        </a>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control form-control-sm" value="{{ request('buscar') }}" placeholder="Buscar por código, nombre o código de barras...">
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select form-select-sm">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Estado</th>
                    @if(auth()->user()->isAdmin())
                        <th>Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $p)
                    <tr>
                        <td><code>{{ $p->codigo }}</code></td>
                        <td>
                            <div class="fw-medium">{{ $p->nombre }}</div>
                            @if($p->codigo_barra)<small class="text-muted">{{ $p->codigo_barra }}</small>@endif
                        </td>
                        <td>{{ $p->categoria?->nombre ?? '—' }}</td>
                        <td>{{ $p->unidad_medida }}</td>
                        <td class="text-end fw-semibold">S/ {{ number_format($p->precio_venta, 2) }}</td>
                        <td class="text-center">
                            @if($p->activo)
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin())
                            <td>
                                <a href="{{ route('productos.edit', $p) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('productos.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay productos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($productos->hasPages())
        <div class="card-footer">{{ $productos->links() }}</div>
    @endif
</div>
@endsection
