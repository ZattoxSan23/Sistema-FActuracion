<?php $__env->startSection('title', 'Caja'); ?>
<?php $__env->startSection('header', 'Gestión de Caja'); ?>

<?php $__env->startSection('content'); ?>
<?php if($cajaAbierta): ?>
    <div class="alert alert-success d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-cash-register me-2"></i>
            <strong>Caja abierta</strong> por <?php echo e($cajaAbierta->usuarioApertura->name); ?>

            desde <?php echo e($cajaAbierta->fecha_apertura->format('d/m/Y H:i')); ?>

            (Apertura: S/ <?php echo e(number_format($cajaAbierta->monto_apertura, 2)); ?>)
        </div>
        <div>
            <a href="<?php echo e(route('caja.movimientos', $cajaAbierta)); ?>" class="btn btn-sm btn-light">
                <i class="fas fa-list me-1"></i>Movimientos
            </a>
            <a href="<?php echo e(route('arqueo.create', $cajaAbierta)); ?>" class="btn btn-sm btn-info text-white">
                <i class="fas fa-coins me-1"></i>Arqueo
            </a>
            <?php if(auth()->user()->isCajera() && $cajaAbierta->user_id_apertura === auth()->id()): ?>
                <a href="<?php echo e(route('caja.cierre', $cajaAbierta)); ?>" class="btn btn-sm btn-danger">
                    <i class="fas fa-lock me-1"></i>Cerrar Caja
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>No hay caja abierta.
        <?php if(auth()->user()->isCajera() || auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('caja.apertura')); ?>" class="btn btn-sm btn-success ms-2">
                <i class="fas fa-plus me-1"></i>Aperturar Caja
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="<?php echo e(request('desde')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?php echo e(request('hasta')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="abierta" <?php echo e(request('estado') === 'abierta' ? 'selected' : ''); ?>>Abierta</option>
                    <option value="cerrada" <?php echo e(request('estado') === 'cerrada' ? 'selected' : ''); ?>>Cerrada</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Cajero</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-filter me-1"></i>Filtrar</button>
                <a href="<?php echo e(route('caja.index')); ?>" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha Apertura</th>
                        <th>Cajero</th>
                        <th>Fecha Cierre</th>
                        <th class="text-end">Apertura</th>
                        <th class="text-end">Ventas</th>
                        <th class="text-end">Efectivo Teórico</th>
                        <th class="text-end">Efectivo Real</th>
                        <th class="text-end">Diferencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $cajas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($c->fecha_apertura->format('d/m/Y H:i')); ?></td>
                            <td><?php echo e($c->usuarioApertura->name); ?></td>
                            <td><?php echo e($c->fecha_cierre?->format('d/m/Y H:i') ?? '—'); ?></td>
                            <td class="text-end">S/ <?php echo e(number_format($c->monto_apertura, 2)); ?></td>
                            <td class="text-end"><?php echo e($c->cantidad_ventas); ?></td>
                            <td class="text-end">S/ <?php echo e(number_format($c->monto_efectivo_teorico, 2)); ?></td>
                            <td class="text-end"><?php echo e($c->monto_efectivo_real !== null ? 'S/ ' . number_format($c->monto_efectivo_real, 2) : '—'); ?></td>
                            <td class="text-end">
                                <?php if($c->diferencia != 0): ?>
                                    <span class="text-<?php echo e($c->diferencia > 0 ? 'success' : 'danger'); ?>">
                                        S/ <?php echo e(number_format($c->diferencia, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($c->estado === 'abierta'): ?>
                                    <span class="badge bg-success">Abierta</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Cerrada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('caja.movimientos', $c)); ?>" class="btn btn-sm btn-outline-primary" title="Ver movimientos">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($c->estado === 'abierta'): ?>
                                    <a href="<?php echo e(route('arqueo.create', $c)); ?>" class="btn btn-sm btn-outline-info" title="Realizar Arqueo">
                                        <i class="fas fa-coins"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if($c->estado === 'cerrada'): ?>
                                    <a href="<?php echo e(route('caja.reporte.pdf', $c)); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="<?php echo e(route('caja.excel', $c)); ?>" class="btn btn-sm btn-outline-success" title="Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="10" class="text-center text-muted py-4">No hay cajas registradas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($cajas->hasPages()): ?>
        <div class="card-footer">
            <?php echo e($cajas->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/caja/index.blade.php ENDPATH**/ ?>