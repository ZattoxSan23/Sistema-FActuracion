@extends('layouts.app')

@section('title', 'Ventas')
@section('header', 'Ventas')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}" placeholder="Desde">
            </div>
            <div class="col-md-3">
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}" placeholder="Hasta">
            </div>
            <div class="col-md-2">
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    <option value="03" {{ request('tipo') == '03' ? 'selected' : '' }}>Boletas</option>
                    <option value="01" {{ request('tipo') == '01' ? 'selected' : '' }}>Facturas</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado_sunat" class="form-select form-select-sm">
                    <option value="">Estado SUNAT</option>
                    <option value="aceptado" {{ request('estado_sunat') == 'aceptado' ? 'selected' : '' }}>Aceptado</option>
                    <option value="pendiente" {{ request('estado_sunat') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="rechazado" {{ request('estado_sunat') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    <option value="excepcion" {{ request('estado_sunat') == 'excepcion' ? 'selected' : '' }}>Excepción</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Buscar...">
                    <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <h6>Total ventas</h6>
            <div class="value">S/ {{ number_format($totales->total ?? 0, 2) }}</div>
            <small class="text-muted">{{ $totales->cantidad ?? 0 }} comprobantes</small>
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
                        <th>Métodos de Pago</th>
                        <th>SUNAT</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->fecha_emision->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}-subtle text-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}">
                                    {{ $venta->correlativo }}
                                </span>
                            </td>
                            <td>
                                @if($venta->cliente)
                                    <div class="fw-medium">{{ $venta->cliente->nombre_razon_social }}</div>
                                    <small class="text-muted">{{ $venta->cliente->documento_completo }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $venta->usuario->name }}</td>
                            <td>
                                @foreach($venta->pagos as $pago)
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $pago->metodo_label }}: S/{{ number_format($pago->monto, 2) }}</span>
                                @endforeach
                            </td>
                            <td>
                                @php
                                    $estados = [
                                        'pendiente' => ['warning', 'Pendiente'],
                                        'enviado' => ['info', 'Enviado'],
                                        'aceptado' => ['success', 'Aceptado'],
                                        'rechazado' => ['danger', 'Rechazado'],
                                        'excepcion' => ['secondary', 'Excepción'],
                                        'anulado' => ['dark', 'Anulado'],
                                    ];
                                    $est = $estados[$venta->estado_sunat] ?? ['secondary', ucfirst($venta->estado_sunat)];
                                @endphp
                                <span class="badge bg-{{ $est[0] }}-subtle text-{{ $est[0] }}">{{ $est[1] }}</span>
                            </td>
                            <td>
                                @if($venta->estado === 'anulada')
                                    <span class="badge bg-dark">Anulada</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">Activa</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">S/ {{ number_format($venta->total, 2) }}</td>
                            <td>
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('ventas.pdf', $venta) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron ventas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ventas->hasPages())
        <div class="card-footer">
            {{ $ventas->links() }}
        </div>
    @endif
</div>
@endsection
