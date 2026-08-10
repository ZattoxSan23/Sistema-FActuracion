<?php $__env->startSection('title', 'Reportes'); ?>
<?php $__env->startSection('header', 'Reportes'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Ventas Hoy</div>
                <div class="fs-4 fw-bold"><?php echo e($kpis['ventas_hoy'] ?? 0); ?></div>
                <div class="small text-muted">S/ <?php echo e(number_format($kpis['monto_hoy'] ?? 0, 2)); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Ventas del Mes</div>
                <div class="fs-4 fw-bold"><?php echo e($kpis['ventas_mes'] ?? 0); ?></div>
                <div class="small text-muted">S/ <?php echo e(number_format($kpis['monto_mes'] ?? 0, 2)); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-secondary">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Clientes Activos</div>
                <div class="fs-4 fw-bold"><?php echo e($kpis['clientes_activos'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="text-uppercase small text-muted">Productos Activos</div>
                <div class="fs-4 fw-bold"><?php echo e($kpis['productos_activos'] ?? 0); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.ventas')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-chart-line text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Reporte de Ventas</h5>
                    <p class="text-muted small">Listado detallado con exportación PDF y Excel</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.flujo.caja')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow border-primary">
                <div class="card-body">
                    <i class="fas fa-exchange-alt text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Flujo de Caja</h5>
                    <p class="text-muted small">Ingresos vs Egresos por día con gráfico</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.resumen.diario')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow border-primary">
                <div class="card-body">
                    <i class="fas fa-calendar-check text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Resumen Diario</h5>
                    <p class="text-muted small">Operaciones del día con KPIs y top productos</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.libro.ventas')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-book text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Libro de Ventas</h5>
                    <p class="text-muted small">Registro contable para declaración a SUNAT</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.productos.mas.vendidos')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-trophy text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Productos Más Vendidos</h5>
                    <p class="text-muted small">Ranking de productos por cantidad e ingresos</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.por.categoria')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-tags text-info" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Ventas por Categoría</h5>
                    <p class="text-muted small">Distribución de ventas por categoría</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.por.vendedor')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-user-tie text-secondary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Ventas por Vendedor</h5>
                    <p class="text-muted small">Productividad de cada cajero/vendedor</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.por.metodo.pago')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-credit-card text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Por Método de Pago</h5>
                    <p class="text-muted small">Análisis de medios de pago</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.diario')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-calendar-day text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Reporte Diario</h5>
                    <p class="text-muted small">Resumen del día actual o cualquier día</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.mensual')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-calendar-alt text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Reporte Mensual</h5>
                    <p class="text-muted small">Resumen por meses del año</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.igv')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-percentage text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Reporte de IGV</h5>
                    <p class="text-muted small">Resumen para declaración tributaria</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.stock.critico')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow border-danger">
                <div class="card-body">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Stock Crítico</h5>
                    <p class="text-muted small">Productos sin stock o bajo mínimo</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.top.clientes')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow">
                <div class="card-body">
                    <i class="fas fa-user-tie text-info" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Top Clientes</h5>
                    <p class="text-muted small">Los 30 clientes con mayor compra</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('reportes.margen.ganancia')); ?>" class="text-decoration-none">
            <div class="card h-100 text-center hover-shadow border-success">
                <div class="card-body">
                    <i class="fas fa-dollar-sign text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Margen de Ganancia</h5>
                    <p class="text-muted small">Utilidad por producto vendido</p>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.hover-shadow { transition: all 0.2s; cursor: pointer; }
.hover-shadow:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.1); transform: translateY(-3px); }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/index.blade.php ENDPATH**/ ?>