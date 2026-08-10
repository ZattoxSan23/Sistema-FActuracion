<?php $__env->startSection('title', 'Reporte de Ventas'); ?>
<?php $__env->startSection('header', 'Reporte de Ventas'); ?>

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
                <label class="form-label small mb-1">Cajero / Vendedor</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $usuarios ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php echo e(($filtros['user_id'] ?? '') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2 justify-content-end">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
                <a href="<?php echo e(route('reportes.ventas.excel', $filtros)); ?>" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </a>
                <a href="<?php echo e(route('reportes.ventas.pdf', $filtros)); ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Total Ventas</div>
                <div class="fs-3 fw-bold">S/ <?php echo e(number_format($totales['total'], 2)); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Cantidad</div>
                <div class="fs-3 fw-bold"><?php echo e($totales['cantidad']); ?></div>
                <small>Boletas: <?php echo e($totales['boletas']); ?> | Facturas: <?php echo e($totales['facturas']); ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-secondary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Op. Gravadas</div>
                <div class="fs-3 fw-bold">S/ <?php echo e(number_format($totales['gravadas'], 2)); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="text-uppercase small text-muted">IGV</div>
                <div class="fs-3 fw-bold">S/ <?php echo e(number_format($totales['igv'], 2)); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Comprobante</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>SUNAT</th>
                        <th class="text-end">Gravadas</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($venta->fecha_emision->format('d/m/Y H:i')); ?></td>
                            <td><span class="badge bg-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>-subtle text-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>"><?php echo e($venta->correlativo); ?></span></td>
                            <td><?php echo e($venta->cliente?->nombre_razon_social ?? '—'); ?></td>
                            <td><?php echo e($venta->usuario?->name ?? '—'); ?></td>
                            <td><small><?php echo e(ucfirst($venta->estado_sunat)); ?></small></td>
                            <td class="text-end"><?php echo e(number_format($venta->op_gravadas, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($venta->igv, 2)); ?></td>
                            <td class="text-end fw-semibold"><?php echo e(number_format($venta->total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Sin ventas en el rango</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/ventas.blade.php ENDPATH**/ ?>