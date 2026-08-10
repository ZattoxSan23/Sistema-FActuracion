@extends('layouts.app')

@section('title', 'Margen de Ganancia')
@section('header', 'Reporte de Margen de Ganancia')

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
            <div class="col-md-8 d-flex gap-2 justify-content-end">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Ingreso Neto</div>
                <div class="fs-3 fw-bold">S/ {{ number_format($datos->sum('ingreso_neto'), 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-danger">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Costo Total</div>
                <div class="fs-3 fw-bold">S/ {{ number_format($datos->sum('costo'), 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Utilidad Bruta</div>
                <div class="fs-3 fw-bold text-success">S/ {{ number_format($datos->sum('utilidad'), 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Margen Promedio</div>
                <div class="fs-3 fw-bold">
                    @php
                        $ing = $datos->sum('ingreso_neto');
                        $util = $datos->sum('utilidad');
                        echo $ing > 0 ? number_format(($util / $ing) * 100, 1) : 0;
                    @endphp%
                </div>
            </div>
        </div>
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
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Ingreso Neto</th>
                    <th class="text-end">Costo</th>
                    <th class="text-end">Utilidad</th>
                    <th class="text-end">Margen %</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $d)
                    <tr>
                        <td><code>{{ $d->codigo }}</code></td>
                        <td>{{ $d->nombre }}</td>
                        <td>{{ $d->categoria ?? '—' }}</td>
                        <td class="text-end">{{ number_format($d->cantidad, 2) }}</td>
                        <td class="text-end">S/ {{ number_format($d->ingreso_neto, 2) }}</td>
                        <td class="text-end text-danger">S/ {{ number_format($d->costo, 2) }}</td>
                        <td class="text-end fw-bold text-success">S/ {{ number_format($d->utilidad, 2) }}</td>
                        <td class="text-end">
                            <span class="badge {{ $d->margen_pct >= 50 ? 'bg-success' : ($d->margen_pct >= 30 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $d->margen_pct }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Sin datos en el periodo</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
