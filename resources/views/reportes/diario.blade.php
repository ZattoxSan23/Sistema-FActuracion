@extends('layouts.app')

@section('title', 'Reporte Diario')
@section('header', 'Reporte Diario')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="date" name="fecha" class="form-control form-control-sm" value="{{ $fecha }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total del día</h6>
            <div class="value">S/ {{ number_format($totales['total'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Ventas</h6>
            <div class="value">{{ $totales['cantidad'] }}</div>
            <small>Boletas: {{ $totales['boletas'] }} | Facturas: {{ $totales['facturas'] }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>IGV Recaudado</h6>
            <div class="value">S/ {{ number_format($totales['igv'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Ticket Promedio</h6>
            <div class="value">S/ {{ $totales['cantidad'] > 0 ? number_format($totales['total'] / $totales['cantidad'], 2) : '0.00' }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Ventas del día</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Hora</th>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td>{{ $venta->fecha_emision->format('H:i') }}</td>
                                <td><span class="badge bg-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}-subtle text-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}">{{ $venta->correlativo }}</span></td>
                                <td>{{ $venta->cliente?->nombre_razon_social ?? '—' }}</td>
                                <td class="text-end fw-semibold">S/ {{ number_format($venta->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin ventas en este día</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Por hora</div>
            <div class="card-body">
                <canvas id="horasChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const porHora = @json($porHora);
new Chart(document.getElementById('horasChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(porHora).map(h => h + ':00'),
        datasets: [{
            label: 'Ventas por hora',
            data: Object.values(porHora).map(v => parseFloat(v.total)),
            backgroundColor: '#2563eb'
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
@endsection
