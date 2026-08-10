@extends('layouts.app')

@section('title', 'Por Método de Pago')
@section('header', 'Reporte por Método de Pago')

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
                <label class="form-label small mb-1">Cajero</label>
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

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Método</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalG = $metodos->sum('total'); @endphp
                        @forelse($metodos as $m)
                            <tr>
                                <td>{{ ucfirst($m->metodo_pago) }}</td>
                                <td class="text-end">{{ $m->cantidad }}</td>
                                <td class="text-end fw-semibold">S/ {{ number_format($m->total, 2) }}</td>
                                <td class="text-end">{{ $totalG > 0 ? number_format(($m->total / $totalG) * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="metodosChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const metodos = @json($metodos);
new Chart(document.getElementById('metodosChart'), {
    type: 'doughnut',
    data: {
        labels: metodos.map(m => m.metodo_pago.charAt(0).toUpperCase() + m.metodo_pago.slice(1)),
        datasets: [{
            data: metodos.map(m => parseFloat(m.total)),
            backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#6b7280']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
@endsection
