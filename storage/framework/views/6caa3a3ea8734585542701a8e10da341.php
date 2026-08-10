<?php
    $user = auth()->user();
    $cajaAbierta = \App\Models\Caja::cajaAbierta();
?>

<header class="header">
    <h1><?php echo $__env->yieldContent('header', 'Dashboard'); ?></h1>

    <div class="ms-auto d-flex align-items-center gap-3">
        <?php if($cajaAbierta && ($user->isAdmin() || $user->isCajera())): ?>
            <div class="badge bg-success-subtle text-success p-2">
                <i class="fas fa-cash-register me-1"></i>
                Caja abierta
                <?php if($cajaAbierta->user_id_apertura === $user->id): ?>
                    (Tuya)
                <?php else: ?>
                    (<?php echo e($cajaAbierta->usuarioApertura->name ?? ''); ?>)
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="dropdown">
            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <div class="user-avatar"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
                <div class="text-start d-none d-md-block">
                    <div class="fw-semibold text-dark" style="font-size: 0.9rem;"><?php echo e($user->name); ?></div>
                    <small class="text-muted text-capitalize"><?php echo e($user->rol); ?></small>
                </div>
                <i class="fas fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?php echo e(route('perfil.edit')); ?>"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
<?php /**PATH /var/www/html/resources/views/layouts/partials/header.blade.php ENDPATH**/ ?>