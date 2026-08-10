@extends('layouts.app')

@section('title', 'Productos Más Vendidos')
@section('header', 'Productos Más Vendidos')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ $filtros['desde'] }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $filtros['hasta'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Categoría</label>
                <select name="categoria_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($categorias ?? [] as $c)
                        <option value="{{ $c->id }}" {{ ($filtros['categoria_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Cajero</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($usuarios ?? [] as $u)
                        <option value="{{ $u->id }}" {{ ($filtros['user_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm w-100"><i class="fas fa-filter me-1"></i>Filtrar</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-end">Cantidad Vendida</th>
                    <th class="text-end">Veces Vendido</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $index => $producto)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $producto->codigo }}</code></td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria ?? '—' }}</td>
                        <td class="text-end fw-semibold">{{ number_format($producto->cantidad_vendida, 2) }}</td>
                        <td class="text-end">{{ $producto->veces_vendido }}</td>
                        <td class="text-end text-success fw-semibold">S/ {{ number_format($producto->total_vendido, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
