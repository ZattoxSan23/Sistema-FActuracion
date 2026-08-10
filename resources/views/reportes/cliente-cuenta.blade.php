@extends('layouts.app')

@section('title', 'Estado de Cuenta - ' . $cliente->nombre_razon_social)
@section('header', 'Estado de Cuenta del Cliente')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="mb-1"><strong>Cliente:</strong> {{ $cliente->nombre_razon_social }}</p>
                <p class="mb-1"><strong>Documento:</strong> {{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}</p>
                @if($cliente->direccion)
                    <p class="mb-1"><strong>Dirección:</strong> {{ $cliente->direccion }}</p>
                @endif
                @if($cliente->telefono)
                    <p class="mb-1"><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Desde</label>
                        <input type="date" name="desde" class="form-control form-control-sm" value="{{ $filtros['desde'] }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Hasta</label>
                        <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $filtros['hasta'] }}">
                    </div>
                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
                        <a href="{{ route('reportes.cliente.cuenta.excel', array_merge(['cliente' => $cliente->id], $filtros)) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-file-excel me-1"></i>Excel
                        </a>
                        <a href="{{ route('reportes.cliente.cuenta.pdf', array_merge(['cliente' => $cliente->id], $filtros)) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-file-pdf me-1"></i>PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Total Compras</div>
                <div class="fs-3 fw-bold">S/ {{ number_format($totales['total'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Cantidad</div>
                <div class="fs-3 fw-bold">{{ $totales['cantidad'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-secondary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Boletas</div>
                <div class="fs-3 fw-bold">{{ $totales['boletas'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Facturas</div>
                <div class="fs-3 fw-bold">{{ $totales['facturas'] }}</div>
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
                        <th>Items</th>
                        <th>Estado SUNAT</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $v)
                        <tr>
                            <td>{{ $v->fecha_emision->format('d/m/Y H:i') }}</td>
                            <td>
                                <code>{{ $v->correlativo }}</code><br>
                                <span class="badge bg-{{ $v->tipo_comprobante === '01' ? 'primary' : 'info' }}-subtle">
                                    {{ $v->tipo_comprobante_label }}
                                </span>
                            </td>
                            <td>{{ $v->items->count() }}</td>
                            <td>
                                @if($v->comprobante)
                                    <span class="badge bg-{{ $v->comprobante->estado === 'aceptado' ? 'success' : ($v->comprobante->estado === 'rechazado' ? 'danger' : 'warning') }}-subtle">
                                        {{ $v->comprobante->estado }}
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td class="text-end fw-semibold">S/ {{ number_format($v->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin compras en el periodo</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
