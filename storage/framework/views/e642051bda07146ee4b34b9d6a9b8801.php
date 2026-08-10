<?php $__env->startSection('title', 'Libro de Ventas'); ?>
<?php $__env->startSection('header', 'Libro de Ventas'); ?>

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
                <a href="<?php echo e(route('reportes.libro.ventas.pdf', $filtros)); ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Fecha Emisión</th>
                        <th>Tipo</th>
                        <th>Serie-Número</th>
                        <th>Tipo Doc.</th>
                        <th>RUC/DNI</th>
                        <th>Cliente</th>
                        <th class="text-end">Gravadas</th>
                        <th class="text-end">Exoneradas</th>
                        <th class="text-end">Inafectas</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($venta->fecha_emision->format('d/m/Y')); ?></td>
                            <td><?php echo e($venta->tipo_comprobante_label); ?></td>
                            <td><?php echo e($venta->correlativo); ?></td>
                            <td><?php echo e($venta->cliente?->tipo_documento ?? '—'); ?></td>
                            <td><?php echo e($venta->cliente?->numero_documento ?? '—'); ?></td>
                            <td><?php echo e($venta->cliente?->nombre_razon_social ?? '—'); ?></td>
                            <td class="text-end"><?php echo e(number_format($venta->op_gravadas, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($venta->op_exoneradas, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($venta->op_inafectas, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($venta->igv, 2)); ?></td>
                            <td class="text-end fw-semibold"><?php echo e(number_format($venta->total, 2)); ?></td>
                            <td>
                                <?php if($venta->comprobante && $venta->comprobante->isAceptado()): ?>
                                    <span class="badge bg-success">Aceptado</span>
                                <?php elseif($venta->estado === 'anulada'): ?>
                                    <span class="badge bg-dark">Anulada</span>
                                <?php else: ?>
                                    <span class="badge bg-warning"><?php echo e(ucfirst($venta->estado_sunat)); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="13" class="text-center text-muted py-4">Sin registros</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="7" class="text-end">TOTALES:</th>
                        <th class="text-end"><?php echo e(number_format($ventas->sum('op_gravadas'), 2)); ?></th>
                        <th class="text-end"><?php echo e(number_format($ventas->sum('op_exoneradas'), 2)); ?></th>
                        <th class="text-end"><?php echo e(number_format($ventas->sum('op_inafectas'), 2)); ?></th>
                        <th class="text-end"><?php echo e(number_format($ventas->sum('igv'), 2)); ?></th>
                        <th class="text-end fw-bold"><?php echo e(number_format($ventas->sum('total'), 2)); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/libro-ventas.blade.php ENDPATH**/ ?>