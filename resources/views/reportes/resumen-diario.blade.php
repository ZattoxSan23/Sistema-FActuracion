@extends('layouts.app')

@section('title', 'Resumen Diario')
@section('header', 'Resumen de Operaciones Diarias')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Fecha</label>
                <input type="date" name="fecha" class="form-control form-control-sm" value="{{ $fecha }}">
            </div>
            <div class="col-md-9 d-flex gap-2 justify-content-end">
                <a href="{{ route('reportes.resumen.diario.excel', ['fecha' => $fecha]) }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </a>
                <a href="{{ route('reportes.resumen.diario.pdf', ['fecha' => $fecha]) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
            </div>
        </form>
    </div>
</div>

<h4 class="mb-3"><i class="fas fa-calendar-day me-2"></i>{{ \Carbon\Carbon::parse($fecha)->format('l, d \\d\\e F \\d\\e Y') }}</h4>

<div class="row g-3 mb-3">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Ventas</div>
                <div class="fs-3 fw-bold">{{ $datos['kpis']['ventas_count'] }}</div>
                <div class="small text-muted">S/ {{ number_format($datos['kpis']['total_ventas'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="text-uppercase small text-muted">IGV Recaudado</div>
                <div class="fs-3 fw-bold text-success">S/ {{ number_format($datos['kpis']['igv'], 2) }}</div>
                <div class="small text-muted">{{ $datos['kpis']['boletas'] }} boletas / {{ $datos['kpis']['facturas'] }} facturas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card stat-card-danger">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Egresos del Día</div>
                <div class="fs-3 fw-bold text-danger">S/ {{ number_format($datos['kpis']['total_egresos'], 2) }}</div>
                <div class="small text-muted">Caja: S/ {{ number_format($datos['kpis']['egresos_caja'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Neto del Día</div>
                <div class="fs-3 fw-bold {{ $datos['kpis']['neto'] >= 0 ? 'text-success' : 'text-danger' }}">S/ {{ number_format($datos['kpis']['neto'], 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Distribución por Método de Pago</div>
            <div class="card-body">
                <canvas id="chartMetodos" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Ventas por Hora</div>
            <div class="card-body">
                <canvas id="chartHoras" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">Top 10 Productos del Día</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($datos['top_productos'] as $i => $p)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $p->nombre }}</td>
                                    <td class="text-end">{{ number_format($p->cantidad, 2) }}</td>
                                    <td class="text-end fw-semibold">S/ {{ number_format($p->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Sin ventas registradas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const porMetodo = @json($datos['por_metodo']);
const porHora = @json($datos['por_hora']);

if (Object.keys(porMetodo).length) {
    new Chart(document.getElementById('chartMetodos'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(porMetodo),
            datasets: [{
                data: Object.values(porMetodo).map(m => m.total),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'],
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
}

if (Object.keys(porHora).length) {
    const horas = Array.from({length: 24}, (_, i) => i.toString().padStart(2, '0'));
    new Chart(document.getElementById('chartHoras'), {
        type: 'bar',
        data: {
            labels: horas,
            datasets: [{
                label: 'Ventas (S/)',
                data: horas.map(h => porMetodo && porHora[h] ? porHora[h].total : 0),
                backgroundColor: '#2563eb',
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
}
</script>
@endpush
