@extends('layouts.app')

@section('title', 'Ventas por Categoría')
@section('header', 'Ventas por Categoría')

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
    <div class="col-md-7">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Categoría</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categorias as $cat)
                            <tr>
                                <td>{{ $cat->nombre ?? 'Sin categoría' }}</td>
                                <td class="text-end">{{ number_format($cat->cantidad_vendida, 2) }}</td>
                                <td class="text-end fw-semibold">S/ {{ number_format($cat->total_vendido, 2) }}</td>
                                <td class="text-end">{{ $totalGeneral > 0 ? number_format(($cat->total_vendido / $totalGeneral) * 100, 1) : 0 }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-end">{{ number_format($categorias->sum('cantidad_vendida'), 2) }}</th>
                            <th class="text-end">S/ {{ number_format($totalGeneral, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Distribución</div>
            <div class="card-body">
                <canvas id="categoriasChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const categorias = @json($categorias);
const total = parseFloat({{ (float) $totalGeneral }});
new Chart(document.getElementById('categoriasChart'), {
    type: 'pie',
    data: {
        labels: categorias.map(c => c.nombre || 'Sin categoría'),
        datasets: [{
            data: categorias.map(c => parseFloat(c.total_vendido)),
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
@endsection
