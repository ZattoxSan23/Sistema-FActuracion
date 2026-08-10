@extends('layouts.app')

@section('title', 'Flujo de Caja')
@section('header', 'Flujo de Caja')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ $filtros['desde'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $filtros['hasta'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Cajero / Vendedor</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ $filtros['user_id'] == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-filter me-1"></i>Filtrar</button>
                <a href="{{ route('reportes.flujo.caja') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Total Ingresos</div>
                <div class="fs-3 fw-bold">S/ {{ number_format(array_sum(array_column($datos, 'ingresos')), 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card stat-card-danger">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Total Egresos</div>
                <div class="fs-3 fw-bold">S/ {{ number_format(array_sum(array_column($datos, 'egresos')), 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Flujo Neto</div>
                <div class="fs-3 fw-bold">S/ {{ number_format(array_sum(array_column($datos, 'neto')), 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Gráfico: Ingresos vs Egresos</span>
        <div>
            <a href="{{ route('reportes.flujo.caja.excel', request()->all()) }}" class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-excel me-1"></i>Excel
            </a>
            <a href="{{ route('reportes.flujo.caja.pdf', request()->all()) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-file-pdf me-1"></i>PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <canvas id="chartFlujo" height="100"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th class="text-end">Ingresos</th>
                        <th class="text-end">Egresos</th>
                        <th class="text-end">Neto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datos as $d)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($d['fecha'])->format('d/m/Y') }}</td>
                            <td class="text-end text-success fw-semibold">S/ {{ number_format($d['ingresos'], 2) }}</td>
                            <td class="text-end text-danger fw-semibold">S/ {{ number_format($d['egresos'], 2) }}</td>
                            <td class="text-end fw-bold {{ $d['neto'] >= 0 ? 'text-success' : 'text-danger' }}">S/ {{ number_format($d['neto'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No hay datos en el periodo</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('chartFlujo');
const datos = @json($datos);
new Chart(ctx, {
    type: 'line',
    data: {
        labels: datos.map(d => d.fecha),
        datasets: [
            { label: 'Ingresos', data: datos.map(d => d.ingresos), borderColor: 'rgb(16, 185, 129)', backgroundColor: 'rgba(16, 185, 129, 0.1)', tension: 0.3, fill: true },
            { label: 'Egresos', data: datos.map(d => d.egresos), borderColor: 'rgb(239, 68, 68)', backgroundColor: 'rgba(239, 68, 68, 0.1)', tension: 0.3, fill: true },
            { label: 'Neto', data: datos.map(d => d.neto), borderColor: 'rgb(37, 99, 235)', backgroundColor: 'rgba(37, 99, 235, 0.1)', tension: 0.3, fill: true, borderDash: [5, 5] }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
