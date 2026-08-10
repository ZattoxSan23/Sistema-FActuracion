<?php $__env->startSection('title', 'Reporte Diario'); ?>
<?php $__env->startSection('header', 'Reporte Diario'); ?>

<?php $__env->startSection('content'); ?>
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="date" name="fecha" class="form-control form-control-sm" value="<?php echo e($fecha); ?>">
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
            <div class="value">S/ <?php echo e(number_format($totales['total'], 2)); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Ventas</h6>
            <div class="value"><?php echo e($totales['cantidad']); ?></div>
            <small>Boletas: <?php echo e($totales['boletas']); ?> | Facturas: <?php echo e($totales['facturas']); ?></small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>IGV Recaudado</h6>
            <div class="value">S/ <?php echo e(number_format($totales['igv'], 2)); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Ticket Promedio</h6>
            <div class="value">S/ <?php echo e($totales['cantidad'] > 0 ? number_format($totales['total'] / $totales['cantidad'], 2) : '0.00'); ?></div>
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
                        <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($venta->fecha_emision->format('H:i')); ?></td>
                                <td><span class="badge bg-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>-subtle text-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>"><?php echo e($venta->correlativo); ?></span></td>
                                <td><?php echo e($venta->cliente?->nombre_razon_social ?? '—'); ?></td>
                                <td class="text-end fw-semibold">S/ <?php echo e(number_format($venta->total, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin ventas en este día</td></tr>
                        <?php endif; ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
const porHora = <?php echo json_encode($porHora, 15, 512) ?>;
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/diario.blade.php ENDPATH**/ ?>