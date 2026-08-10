@extends('layouts.app')

@section('title', 'Reporte de Ventas')
@section('header', 'Reporte de Ventas')

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
                <label class="form-label small mb-1">Cajero / Vendedor</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($usuarios ?? [] as $u)
                        <option value="{{ $u->id }}" {{ ($filtros['user_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2 justify-content-end">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
                <a href="{{ route('reportes.ventas.excel', $filtros) }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </a>
                <a href="{{ route('reportes.ventas.pdf', $filtros) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Total Ventas</div>
                <div class="fs-3 fw-bold">S/ {{ number_format($totales['total'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Cantidad</div>
                <div class="fs-3 fw-bold">{{ $totales['cantidad'] }}</div>
                <small>Boletas: {{ $totales['boletas'] }} | Facturas: {{ $totales['facturas'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-secondary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Op. Gravadas</div>
                <div class="fs-3 fw-bold">S/ {{ number_format($totales['gravadas'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="text-uppercase small text-muted">IGV</div>
                <div class="fs-3 fw-bold">S/ {{ number_format($totales['igv'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Comprobante</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>SUNAT</th>
                        <th class="text-end">Gravadas</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->fecha_emision->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}-subtle text-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}">{{ $venta->correlativo }}</span></td>
                            <td>{{ $venta->cliente?->nombre_razon_social ?? '—' }}</td>
                            <td>{{ $venta->usuario?->name ?? '—' }}</td>
                            <td><small>{{ ucfirst($venta->estado_sunat) }}</small></td>
                            <td class="text-end">{{ number_format($venta->op_gravadas, 2) }}</td>
                            <td class="text-end">{{ number_format($venta->igv, 2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($venta->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Sin ventas en el rango</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
