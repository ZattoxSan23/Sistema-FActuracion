@extends('layouts.app')

@section('title', 'Stock Crítico')
@section('header', 'Reporte de Stock Crítico')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card stat-card stat-card-danger">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Sin Stock</div>
                <div class="fs-3 fw-bold">{{ $productos->where('estado_stock', 'sin_stock')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Stock Crítico</div>
                <div class="fs-3 fw-bold">{{ $productos->where('estado_stock', 'critico')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card stat-card-secondary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Total productos a reponer</div>
                <div class="fs-3 fw-bold">{{ $productos->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Umbral stock crítico</label>
                <input type="number" step="0.01" min="0" name="umbral" class="form-control form-control-sm" value="{{ $umbral }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Actualizar</button>
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
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-end">Stock</th>
                    <th class="text-end">Mínimo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $p)
                    <tr>
                        <td><code>{{ $p->codigo }}</code></td>
                        <td>{{ $p->nombre }}</td>
                        <td>{{ $p->categoria?->nombre ?? '—' }}</td>
                        <td class="text-end fw-bold">{{ number_format($p->stock_actual, 2) }}</td>
                        <td class="text-end">{{ number_format($p->stock_minimo, 2) }}</td>
                        <td>
                            @if($p->estado_stock === 'sin_stock')
                                <span class="badge bg-danger">Sin Stock</span>
                            @else
                                <span class="badge bg-warning text-dark">Crítico</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay productos con stock crítico. ¡Bien!</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
