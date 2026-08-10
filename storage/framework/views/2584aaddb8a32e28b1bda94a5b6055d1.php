<?php $__env->startSection('title', 'Por Método de Pago'); ?>
<?php $__env->startSection('header', 'Reporte por Método de Pago'); ?>

<?php $__env->startSection('content'); ?>
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="<?php echo e($filtros['desde']); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?php echo e($filtros['hasta']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Cajero</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $usuarios ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php echo e(($filtros['user_id'] ?? '') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <?php $totalG = $metodos->sum('total'); ?>
                        <?php $__empty_1 = true; $__currentLoopData = $metodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e(ucfirst($m->metodo_pago)); ?></td>
                                <td class="text-end"><?php echo e($m->cantidad); ?></td>
                                <td class="text-end fw-semibold">S/ <?php echo e(number_format($m->total, 2)); ?></td>
                                <td class="text-end"><?php echo e($totalG > 0 ? number_format(($m->total / $totalG) * 100, 1) : 0); ?>%</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Sin datos</td></tr>
                        <?php endif; ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
const metodos = <?php echo json_encode($metodos, 15, 512) ?>;
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/por-metodo-pago.blade.php ENDPATH**/ ?>