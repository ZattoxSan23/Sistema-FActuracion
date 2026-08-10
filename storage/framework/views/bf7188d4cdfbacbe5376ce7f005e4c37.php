<?php
    $user = auth()->user();
    $cajaAbierta = \App\Models\Caja::cajaAbierta();
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-file-invoice-dollar"></i>
        <span><?php echo e(\App\Models\Empresa::actual()?->nombre_comercial ?? 'Facturación'); ?></span>
    </div>

    <nav class="py-2">
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <?php if($user->isAdmin() || $user->isCajera()): ?>
                <li class="sidebar-section">Operaciones</li>

                <li>
                    <a href="<?php echo e(route('pos.index')); ?>" class="<?php echo e(request()->routeIs('pos.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cash-register"></i>
                        <span>Punto de Venta</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo e(route('caja.index')); ?>" class="<?php echo e(request()->routeIs('caja.*') || request()->routeIs('arqueo.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cash-register"></i>
                        <span>Caja</span>
                        <?php if($cajaAbierta && $user->isCajera()): ?>
                            <span class="badge bg-success ms-auto">Abierta</span>
                        <?php endif; ?>
                    </a>
                </li>

                <li>
                    <a href="<?php echo e(route('ventas.index')); ?>" class="<?php echo e(request()->routeIs('ventas.*') ? 'active' : ''); ?>">
                        <i class="fas fa-receipt"></i>
                        <span>Ventas</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="sidebar-section">Catálogo</li>

            <li>
                <a href="<?php echo e(route('productos.index')); ?>" class="<?php echo e(request()->routeIs('productos.*') ? 'active' : ''); ?>">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
            </li>

            <?php if($user->isAdmin()): ?>
                <li>
                    <a href="<?php echo e(route('categorias.index')); ?>" class="<?php echo e(request()->routeIs('categorias.*') ? 'active' : ''); ?>">
                        <i class="fas fa-tags"></i>
                        <span>Categorías</span>
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo e(route('clientes.index')); ?>" class="<?php echo e(request()->routeIs('clientes.*') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>

            <?php if($user->isAdmin() || $user->isContador()): ?>
                <li class="sidebar-section">Reportes</li>

                <li>
                    <a href="<?php echo e(route('reportes.index')); ?>" class="<?php echo e(request()->routeIs('reportes.*') ? 'active' : ''); ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Reportes</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if($user->isAdmin()): ?>
                <li class="sidebar-section">Administración</li>

                <li>
                    <a href="<?php echo e(route('sunat.configuracion')); ?>" class="<?php echo e(request()->routeIs('sunat.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>SUNAT</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo e(route('configuracion.index')); ?>" class="<?php echo e(request()->routeIs('configuracion.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cog"></i>
                        <span>Configuración</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo e(route('usuarios.index')); ?>" class="<?php echo e(request()->routeIs('usuarios.*') ? 'active' : ''); ?>">
                        <i class="fas fa-user-shield"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="sidebar-section">Cuenta</li>

            <li>
                <a href="<?php echo e(route('perfil.edit')); ?>" class="<?php echo e(request()->routeIs('perfil.*') ? 'active' : ''); ?>">
                    <i class="fas fa-user-circle"></i>
                    <span>Mi Perfil</span>
                </a>
            </li>

            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
            </li>
        </ul>
    </nav>
</aside>
<?php /**PATH /var/www/html/resources/views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>