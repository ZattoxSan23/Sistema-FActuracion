<?php $__env->startSection('title', 'Ventas'); ?>
<?php $__env->startSection('header', 'Ventas'); ?>

<?php $__env->startSection('content'); ?>
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="date" name="desde" class="form-control form-control-sm" value="<?php echo e(request('desde')); ?>" placeholder="Desde">
            </div>
            <div class="col-md-3">
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?php echo e(request('hasta')); ?>" placeholder="Hasta">
            </div>
            <div class="col-md-2">
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    <option value="03" <?php echo e(request('tipo') == '03' ? 'selected' : ''); ?>>Boletas</option>
                    <option value="01" <?php echo e(request('tipo') == '01' ? 'selected' : ''); ?>>Facturas</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado_sunat" class="form-select form-select-sm">
                    <option value="">Estado SUNAT</option>
                    <option value="aceptado" <?php echo e(request('estado_sunat') == 'aceptado' ? 'selected' : ''); ?>>Aceptado</option>
                    <option value="pendiente" <?php echo e(request('estado_sunat') == 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                    <option value="rechazado" <?php echo e(request('estado_sunat') == 'rechazado' ? 'selected' : ''); ?>>Rechazado</option>
                    <option value="excepcion" <?php echo e(request('estado_sunat') == 'excepcion' ? 'selected' : ''); ?>>Excepción</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <input type="text" name="buscar" class="form-control" value="<?php echo e(request('buscar')); ?>" placeholder="Buscar...">
                    <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <h6>Total ventas</h6>
            <div class="value">S/ <?php echo e(number_format($totales->total ?? 0, 2)); ?></div>
            <small class="text-muted"><?php echo e($totales->cantidad ?? 0); ?> comprobantes</small>
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
                        <th>Métodos de Pago</th>
                        <th>SUNAT</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($venta->fecha_emision->format('d/m/Y H:i')); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>-subtle text-<?php echo e($venta->tipo_comprobante === '01' ? 'primary' : 'success'); ?>">
                                    <?php echo e($venta->correlativo); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($venta->cliente): ?>
                                    <div class="fw-medium"><?php echo e($venta->cliente->nombre_razon_social); ?></div>
                                    <small class="text-muted"><?php echo e($venta->cliente->documento_completo); ?></small>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($venta->usuario->name); ?></td>
                            <td>
                                <?php $__currentLoopData = $venta->pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-secondary-subtle text-secondary"><?php echo e($pago->metodo_label); ?>: S/<?php echo e(number_format($pago->monto, 2)); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
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
                            <td>
                                <?php if($venta->estado === 'anulada'): ?>
                                    <span class="badge bg-dark">Anulada</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success">Activa</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-semibold">S/ <?php echo e(number_format($venta->total, 2)); ?></td>
                            <td>
                                <a href="<?php echo e(route('ventas.show', $venta)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('ventas.pdf', $venta)); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron ventas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($ventas->hasPages()): ?>
        <div class="card-footer">
            <?php echo e($ventas->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/ventas/index.blade.php ENDPATH**/ ?>