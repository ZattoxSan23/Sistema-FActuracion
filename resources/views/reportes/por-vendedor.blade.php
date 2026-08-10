@extends('layouts.app')

@section('title', 'Ventas por Vendedor')
@section('header', 'Ventas por Vendedor/Cajero')

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
                <label class="form-label small mb-1">Cajero específico</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($usuarios ?? [] as $u)
                        <option value="{{ $u->id }}" {{ ($filtros['user_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2 justify-content-end">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vendedor</th>
                    <th>Rol</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Ticket Promedio</th>
                    <th class="text-end">Total Vendido</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendedores as $v)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $v->name }}</div>
                            <small class="text-muted">{{ $v->email }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ ucfirst($v->rol) }}</span></td>
                        <td class="text-end">{{ $v->cantidad_ventas }}</td>
                        <td class="text-end">S/ {{ $v->cantidad_ventas > 0 ? number_format($v->total_vendido / $v->cantidad_ventas, 2) : '0.00' }}</td>
                        <td class="text-end fw-bold text-success">S/ {{ number_format($v->total_vendido, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Sin datos</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
