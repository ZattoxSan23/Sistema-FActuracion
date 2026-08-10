<?php $__env->startSection('title', 'Categorías'); ?>
<?php $__env->startSection('header', 'Categorías'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Orden</th>
                            <th>Nombre</th>
                            <th class="text-center">Productos</th>
                            <th>Color</th>
                            <th>Icono</th>
                            <th class="text-center">Activo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($cat->orden); ?></td>
                                <td class="fw-medium"><?php echo e($cat->nombre); ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?php echo e($cat->productos_count); ?></span></td>
                                <td><span style="display:inline-block; width: 20px; height: 20px; background: <?php echo e($cat->color); ?>; border-radius: 4px;"></span></td>
                                <td><?php echo e($cat->icono); ?></td>
                                <td class="text-center">
                                    <?php if($cat->activo): ?>
                                        <span class="badge bg-success">Sí</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editarCategoria(<?php echo e($cat); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="<?php echo e(route('categorias.destroy', $cat)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Sin categorías</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header" id="form-title">Nueva Categoría</div>
            <div class="card-body">
                <form id="categoria-form" action="<?php echo e(route('categorias.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <div class="mb-2">
                        <label class="form-label small">Nombre</label>
                        <input type="text" name="nombre" id="cat-nombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Descripción</label>
                        <input type="text" name="descripcion" id="cat-descripcion" class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Color</label>
                        <input type="color" name="color" id="cat-color" class="form-control form-control-color form-control-sm" value="#3b82f6">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Icono (FontAwesome)</label>
                        <input type="text" name="icono" id="cat-icono" class="form-control form-control-sm" placeholder="fas fa-tag">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Orden</label>
                        <input type="number" name="orden" id="cat-orden" class="form-control form-control-sm" value="0">
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="activo" value="1" id="cat-activo" class="form-check-input" checked>
                        <label for="cat-activo" class="form-check-label small">Activo</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Guardar</button>
                    <button type="button" class="btn btn-link btn-sm w-100" id="btn-cancelar" onclick="cancelarEdicion()" style="display:none;">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let editandoId = null;

function editarCategoria(cat) {
    document.getElementById('form-title').textContent = 'Editar Categoría';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('categoria-form').action = `/categorias/${cat.id}`;
    document.getElementById('cat-nombre').value = cat.nombre;
    document.getElementById('cat-descripcion').value = cat.descripcion || '';
    document.getElementById('cat-color').value = cat.color || '#3b82f6';
    document.getElementById('cat-icono').value = cat.icono || '';
    document.getElementById('cat-orden').value = cat.orden;
    document.getElementById('cat-activo').checked = cat.activo;
    document.getElementById('btn-cancelar').style.display = 'block';
    editandoId = cat.id;
}

function cancelarEdicion() {
    document.getElementById('form-title').textContent = 'Nueva Categoría';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('categoria-form').action = '<?php echo e(route('categorias.store')); ?>';
    document.getElementById('categoria-form').reset();
    document.getElementById('btn-cancelar').style.display = 'none';
    editandoId = null;
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/categorias/index.blade.php ENDPATH**/ ?>