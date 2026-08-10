@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    @if($data['tipo'] === 'completo')
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-sun"></i>
                        </div>
                    </div>
                    <h6>Ventas hoy</h6>
                    <div class="value">S/ {{ number_format($data['ventas_hoy'], 2) }}</div>
                    <small class="text-muted">{{ $data['cantidad_ventas_hoy'] }} ventas</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <h6>Ventas del mes</h6>
                    <div class="value">S/ {{ number_format($data['ventas_mes'], 2) }}</div>
                    <small class="text-muted">{{ $data['cantidad_ventas_mes'] }} ventas</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <h6>Ticket promedio</h6>
                    <div class="value">S/ {{ number_format($data['ticket_promedio'], 2) }}</div>
                    <small class="text-muted">Hoy</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <h6>Pendientes SUNAT</h6>
                    <div class="value">{{ $data['pendientes_sunat'] }}</div>
                    <small class="text-muted">Comprobantes</small>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <span><i class="fas fa-chart-line me-2"></i>Ventas últimos 7 días</span>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasPorDiaChart" height="80"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clock me-2"></i>Últimas Ventas
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hora</th>
                                        <th>Comprobante</th>
                                        <th>Cliente</th>
                                        <th>Vendedor</th>
                                        <th>Estado SUNAT</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['ultimas_ventas'] as $venta)
                                        <tr>
                                            <td>{{ $venta->fecha_emision->format('H:i') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}-subtle text-{{ $venta->tipo_comprobante === '01' ? 'primary' : 'success' }}">
                                                    {{ $venta->correlativo }}
                                                </span>
                                            </td>
                                            <td>{{ $venta->cliente?->nombre_razon_social ?? '—' }}</td>
                                            <td>{{ $venta->usuario->name }}</td>
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
                                            <td class="text-end fw-semibold">S/ {{ number_format($venta->total, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">No hay ventas registradas</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-trophy me-2"></i>Top Productos del Mes
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($data['top_productos']->take(8) as $index => $producto)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                        <small class="text-muted">{{ $producto->codigo }}</small>
                                        <div class="fw-semibold">{{ $producto->nombre }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">{{ number_format($producto->cantidad_vendida, 0) }} und</div>
                                        <small class="text-success">S/ {{ number_format($producto->total_vendido, 2) }}</small>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-4">Sin datos</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i>Por tipo de comprobante
                    </div>
                    <div class="card-body">
                        <canvas id="ventasPorTipoChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            const ventasPorDia = @json($data['ventas_por_dia']);
            new Chart(document.getElementById('ventasPorDiaChart'), {
                type: 'line',
                data: {
                    labels: ventasPorDia.map(v => new Date(v.fecha).toLocaleDateString('es-PE', {day: '2-digit', month: 'short'})),
                    datasets: [{
                        label: 'Total ventas',
                        data: ventasPorDia.map(v => parseFloat(v.total)),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => 'S/ ' + v.toFixed(0) }
                        }
                    }
                }
            });

            const ventasPorTipo = @json($data['ventas_por_tipo']);
            const tipoLabels = {'01': 'Facturas', '03': 'Boletas', '07': 'N. Crédito', '08': 'N. Débito'};
            new Chart(document.getElementById('ventasPorTipoChart'), {
                type: 'doughnut',
                data: {
                    labels: ventasPorTipo.map(v => tipoLabels[v.tipo_comprobante] || v.tipo_comprobante),
                    datasets: [{
                        data: ventasPorTipo.map(v => parseFloat(v.total)),
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        </script>
        @endpush

    @else
        {{-- Dashboard Cajera --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="stat-card">
                    <h6>Ventas hoy</h6>
                    <div class="value">S/ {{ number_format($data['ventas_hoy'], 2) }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <h6>Ventas totales</h6>
                    <div class="value">{{ $data['ventas_usuario'] }}</div>
                </div>
            </div>
        </div>

        @if($data['caja'])
            <div class="card mt-3">
                <div class="card-body">
                    <h5><i class="fas fa-cash-register me-2 text-success"></i>Caja Abierta</h5>
                    <p class="mb-1"><strong>Apertura:</strong> {{ $data['caja']->fecha_apertura->format('d/m/Y H:i') }}</p>
                    <p class="mb-1"><strong>Monto apertura:</strong> S/ {{ number_format($data['caja']->monto_apertura, 2) }}</p>
                    <a href="{{ route('caja.cierre', $data['caja']) }}" class="btn btn-danger mt-2">
                        <i class="fas fa-lock me-1"></i>Cerrar Caja
                    </a>
                    <a href="{{ route('pos.index') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-cash-register me-1"></i>Ir al POS
                    </a>
                </div>
            </div>
        @else
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No tienes una caja abierta. <a href="{{ route('caja.apertura') }}" class="alert-link">Abrir caja ahora</a>
            </div>
        @endif
    @endif
@endsection
