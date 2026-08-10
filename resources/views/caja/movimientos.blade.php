@extends('layouts.app')

@section('title', 'Movimientos de Caja #' . $caja->id)
@section('header', 'Movimientos de Caja #' . $caja->id)

@section('content')
<div class="page-title">
    <h2><i class="fas fa-list me-2"></i>Movimientos de Caja</h2>
    <a href="{{ route('caja.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Apertura</h6>
            <div class="value">S/ {{ number_format($caja->monto_apertura, 2) }}</div>
            <small>{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Ventas</h6>
            <div class="value">{{ $caja->cantidad_ventas }}</div>
            <small>Total: S/ {{ number_format($caja->total_ventas_efectivo + $caja->total_ventas_tarjeta + $caja->total_ventas_yape + $caja->total_ventas_transferencia, 2) }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Ingresos / Egresos</h6>
            <div class="value">+{{ number_format($caja->total_ingresos, 0) }} / -{{ number_format($caja->total_egresos, 0) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Efectivo Teórico</h6>
            <div class="value">S/ {{ number_format($caja->monto_efectivo_teorico, 2) }}</div>
            <small>{{ $caja->estado === 'cerrada' ? 'Cerrada' : 'Abierta' }}</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Método</th>
                        <th>Usuario</th>
                        <th class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($caja->movimientos as $mov)
                        <tr>
                            <td>{{ $mov->fecha->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <span class="badge bg-{{ $mov->tipo === 'ingreso' ? 'success' : ($mov->tipo === 'venta' ? 'primary' : 'danger') }}-subtle text-{{ $mov->tipo === 'ingreso' ? 'success' : ($mov->tipo === 'venta' ? 'primary' : 'danger') }}">
                                    {{ $mov->tipo_label }}
                                </span>
                            </td>
                            <td>
                                {{ $mov->concepto }}
                                @if($mov->referencia)
                                    <br><small class="text-muted">{{ $mov->referencia }}</small>
                                @endif
                            </td>
                            <td>{{ ucfirst($mov->metodo_pago) }}</td>
                            <td>{{ $mov->usuario->name }}</td>
                            <td class="text-end fw-semibold">
                                <span class="text-{{ in_array($mov->tipo, ['ingreso', 'venta']) ? 'success' : 'danger' }}">
                                    {{ in_array($mov->tipo, ['ingreso', 'venta']) ? '+' : '-' }} S/ {{ number_format($mov->monto, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No hay movimientos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
