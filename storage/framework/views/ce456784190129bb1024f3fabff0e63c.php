<?php $__env->startSection('title', 'Productos'); ?>
<?php $__env->startSection('header', 'Productos'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-title">
    <h2><i class="fas fa-box me-2"></i>Productos</h2>
    <?php if(auth()->user()->isAdmin()): ?>
        <a href="<?php echo e(route('productos.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Nuevo Producto
        </a>
    <?php endif; ?>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control form-control-sm" value="<?php echo e(request('buscar')); ?>" placeholder="Buscar por código, nombre o código de barras...">
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select form-select-sm">
                    <option value="">Todas las categorías</option>
                    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('categoria_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->nombre); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Estado</th>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><code><?php echo e($p->codigo); ?></code></td>
                        <td>
                            <div class="fw-medium"><?php echo e($p->nombre); ?></div>
                            <?php if($p->codigo_barra): ?><small class="text-muted"><?php echo e($p->codigo_barra); ?></small><?php endif; ?>
                        </td>
                        <td><?php echo e($p->categoria?->nombre ?? '—'); ?></td>
                        <td><?php echo e($p->unidad_medida); ?></td>
                        <td class="text-end fw-semibold">S/ <?php echo e(number_format($p->precio_venta, 2)); ?></td>
                        <td class="text-center">
                            <?php if($p->activo): ?>
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <?php if(auth()->user()->isAdmin()): ?>
                            <td>
                                <a href="<?php echo e(route('productos.edit', $p)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('productos.destroy', $p)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay productos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($productos->hasPages()): ?>
        <div class="card-footer"><?php echo e($productos->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/productos/index.blade.php ENDPATH**/ ?>