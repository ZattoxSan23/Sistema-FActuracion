<?php $__env->startSection('title', 'Ventas por Vendedor'); ?>
<?php $__env->startSection('header', 'Ventas por Vendedor/Cajero'); ?>

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
                <label class="form-label small mb-1">Cajero específico</label>
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

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vendedor</th>
                    <th>Rol</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Ticket Promedio</th>
                    <th class="text-end">Total Vendido</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $vendedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?php echo e($v->name); ?></div>
                            <small class="text-muted"><?php echo e($v->email); ?></small>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo e(ucfirst($v->rol)); ?></span></td>
                        <td class="text-end"><?php echo e($v->cantidad_ventas); ?></td>
                        <td class="text-end">S/ <?php echo e($v->cantidad_ventas > 0 ? number_format($v->total_vendido / $v->cantidad_ventas, 2) : '0.00'); ?></td>
                        <td class="text-end fw-bold text-success">S/ <?php echo e(number_format($v->total_vendido, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Sin datos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/por-vendedor.blade.php ENDPATH**/ ?>