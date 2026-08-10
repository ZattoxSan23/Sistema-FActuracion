@extends('layouts.app')

@section('title', 'Caja')
@section('header', 'Gestión de Caja')

@section('content')
@if($cajaAbierta)
    <div class="alert alert-success d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-cash-register me-2"></i>
            <strong>Caja abierta</strong> por {{ $cajaAbierta->usuarioApertura->name }}
            desde {{ $cajaAbierta->fecha_apertura->format('d/m/Y H:i') }}
            (Apertura: S/ {{ number_format($cajaAbierta->monto_apertura, 2) }})
        </div>
        <div>
            <a href="{{ route('caja.movimientos', $cajaAbierta) }}" class="btn btn-sm btn-light">
                <i class="fas fa-list me-1"></i>Movimientos
            </a>
            <a href="{{ route('arqueo.create', $cajaAbierta) }}" class="btn btn-sm btn-info text-white">
                <i class="fas fa-coins me-1"></i>Arqueo
            </a>
            @if(auth()->user()->isCajera() && $cajaAbierta->user_id_apertura === auth()->id())
                <a href="{{ route('caja.cierre', $cajaAbierta) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-lock me-1"></i>Cerrar Caja
                </a>
            @endif
        </div>
    </div>
@else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>No hay caja abierta.
        @if(auth()->user()->isCajera() || auth()->user()->isAdmin())
            <a href="{{ route('caja.apertura') }}" class="btn btn-sm btn-success ms-2">
                <i class="fas fa-plus me-1"></i>Aperturar Caja
            </a>
        @endif
    </div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="abierta" {{ request('estado') === 'abierta' ? 'selected' : '' }}>Abierta</option>
                    <option value="cerrada" {{ request('estado') === 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Cajero</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-filter me-1"></i>Filtrar</button>
                <a href="{{ route('caja.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha Apertura</th>
                        <th>Cajero</th>
                        <th>Fecha Cierre</th>
                        <th class="text-end">Apertura</th>
                        <th class="text-end">Ventas</th>
                        <th class="text-end">Efectivo Teórico</th>
                        <th class="text-end">Efectivo Real</th>
                        <th class="text-end">Diferencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cajas as $c)
                        <tr>
                            <td>{{ $c->fecha_apertura->format('d/m/Y H:i') }}</td>
                            <td>{{ $c->usuarioApertura->name }}</td>
                            <td>{{ $c->fecha_cierre?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="text-end">S/ {{ number_format($c->monto_apertura, 2) }}</td>
                            <td class="text-end">{{ $c->cantidad_ventas }}</td>
                            <td class="text-end">S/ {{ number_format($c->monto_efectivo_teorico, 2) }}</td>
                            <td class="text-end">{{ $c->monto_efectivo_real !== null ? 'S/ ' . number_format($c->monto_efectivo_real, 2) : '—' }}</td>
                            <td class="text-end">
                                @if($c->diferencia != 0)
                                    <span class="text-{{ $c->diferencia > 0 ? 'success' : 'danger' }}">
                                        S/ {{ number_format($c->diferencia, 2) }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($c->estado === 'abierta')
                                    <span class="badge bg-success">Abierta</span>
                                @else
                                    <span class="badge bg-secondary">Cerrada</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('caja.movimientos', $c) }}" class="btn btn-sm btn-outline-primary" title="Ver movimientos">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($c->estado === 'abierta')
                                    <a href="{{ route('arqueo.create', $c) }}" class="btn btn-sm btn-outline-info" title="Realizar Arqueo">
                                        <i class="fas fa-coins"></i>
                                    </a>
                                @endif
                                @if($c->estado === 'cerrada')
                                    <a href="{{ route('caja.reporte.pdf', $c) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('caja.excel', $c) }}" class="btn btn-sm btn-outline-success" title="Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No hay cajas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($cajas->hasPages())
        <div class="card-footer">
            {{ $cajas->links() }}
        </div>
    @endif
</div>
@endsection
