@extends('layouts.app')

@section('title', 'Top Clientes')
@section('header', 'Top Clientes')

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

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Documento</th>
                    <th>Cliente</th>
                    <th class="text-end">Compras</th>
                    <th class="text-end">Ticket Promedio</th>
                    <th class="text-end">Total</th>
                    <th>Última Compra</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $i => $c)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $c->tipo_documento }}</span>
                            <code>{{ $c->numero_documento }}</code>
                        </td>
                        <td class="fw-semibold">{{ $c->nombre_razon_social }}</td>
                        <td class="text-end">{{ $c->cantidad_compras }}</td>
                        <td class="text-end">S/ {{ $c->cantidad_compras > 0 ? number_format($c->total_comprado / $c->cantidad_compras, 2) : '0.00' }}</td>
                        <td class="text-end fw-bold text-success">S/ {{ number_format($c->total_comprado, 2) }}</td>
                        <td>{{ $c->ultima_compra ? \Carbon\Carbon::parse($c->ultima_compra)->format('d/m/Y') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin datos en el periodo</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
