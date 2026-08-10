<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('header', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <?php if($data['tipo'] === 'completo'): ?>
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-sun"></i>
                        </div>
                    </div>
                    <h6>Ventas hoy</h6>
                    <div class="value">S/ <?php echo e(number_format($data['ventas_hoy'], 2)); ?></div>
                    <small class="text-muted"><?php echo e($data['cantidad_ventas_hoy']); ?> ventas</small>
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
                    <div class="value">S/ <?php echo e(number_format($data['ventas_mes'], 2)); ?></div>
                    <small class="text-muted"><?php echo e($data['cantidad_ventas_mes']); ?> ventas</small>
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
                    <div class="value">S/ <?php echo e(number_format($data['ticket_promedio'], 2)); ?></div>
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
                    <div class="value"><?php echo e($data['pendientes_sunat']); ?></div>
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
                                    <?php $__empty_1 = true; $__currentLoopData = $data['ultimas_ventas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($venta->fecha_emision->format('H:i')); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>-subtle text-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>">
                                                    <?php echo e($venta->correlativo); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($venta->cliente?->nombre_razon_social ?? '—'); ?></td>
                                            <td><?php echo e($venta->usuario->name); ?></td>
                                            <td>
                                                <?php
                                                    $estados = [
                                                        'pendiente' => ['warning', 'Pendiente'],
                                                        'enviado' => ['info', 'Enviado'],
                                                        'aceptado' => ['success', 'Aceptado'],
                                                        'rechazado' => ['danger', 'Rechazado'],
                                                        'excepcion' => ['secondary', 'Excepción'],
                                                        'anulado' => ['dark', 'Anulado'],
                                                    ];
                                                    $est = $estados[$venta->estado_sunat] ?? ['secondary', ucfirst($venta->estado_sunat)];
                                                ?>
                                                <span class="badge bg-<?php echo e($est[0]); ?>-subtle text-<?php echo e($est[0]); ?>"><?php echo e($est[1]); ?></span>
                                            </td>
                                            <td class="text-end fw-semibold">S/ <?php echo e(number_format($venta->total, 2)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4">No hay ventas registradas</td></tr>
                                    <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $data['top_productos']->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2"><?php echo e($index + 1); ?></span>
                                        <small class="text-muted"><?php echo e($producto->codigo); ?></small>
                                        <div class="fw-semibold"><?php echo e($producto->nombre); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold"><?php echo e(number_format($producto->cantidad_vendida, 0)); ?> und</div>
                                        <small class="text-success">S/ <?php echo e(number_format($producto->total_vendido, 2)); ?></small>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <li class="list-group-item text-center text-muted py-4">Sin datos</li>
                            <?php endif; ?>
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

        <?php $__env->startPush('scripts'); ?>
        <script>
            const ventasPorDia = <?php echo json_encode($data['ventas_por_dia'], 15, 512) ?>;
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

            const ventasPorTipo = <?php echo json_encode($data['ventas_por_tipo'], 15, 512) ?>;
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
        <?php $__env->stopPush(); ?>

    <?php else: ?>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="stat-card">
                    <h6>Ventas hoy</h6>
                    <div class="value">S/ <?php echo e(number_format($data['ventas_hoy'], 2)); ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <h6>Ventas totales</h6>
                    <div class="value"><?php echo e($data['ventas_usuario']); ?></div>
                </div>
            </div>
        </div>

        <?php if($data['caja']): ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h5><i class="fas fa-cash-register me-2 text-success"></i>Caja Abierta</h5>
                    <p class="mb-1"><strong>Apertura:</strong> <?php echo e($data['caja']->fecha_apertura->format('d/m/Y H:i')); ?></p>
                    <p class="mb-1"><strong>Monto apertura:</strong> S/ <?php echo e(number_format($data['caja']->monto_apertura, 2)); ?></p>
                    <a href="<?php echo e(route('caja.cierre', $data['caja'])); ?>" class="btn btn-danger mt-2">
                        <i class="fas fa-lock me-1"></i>Cerrar Caja
                    </a>
                    <a href="<?php echo e(route('pos.index')); ?>" class="btn btn-primary mt-2">
                        <i class="fas fa-cash-register me-1"></i>Ir al POS
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No tienes una caja abierta. <a href="<?php echo e(route('caja.apertura')); ?>" class="alert-link">Abrir caja ahora</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/dashboard/index.blade.php ENDPATH**/ ?>