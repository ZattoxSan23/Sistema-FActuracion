<?php $__env->startSection('title', 'Reporte Mensual'); ?>
<?php $__env->startSection('header', 'Reporte Mensual'); ?>

<?php $__env->startSection('content'); ?>
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="año" class="form-select form-select-sm">
                    <?php for($y = now()->year; $y >= now()->year - 5; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($año == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mes</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">IGV</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nombres = ['1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio',
                                '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
                ?>
                <?php for($m = 1; $m <= 12; $m++): ?>
                    <?php
                        $data = $porMes[$m] ?? ['cantidad' => 0, 'total' => 0, 'igv' => 0];
                    ?>
                    <tr>
                        <td><strong><?php echo e($nombres[$m]); ?></strong></td>
                        <td class="text-end"><?php echo e($data['cantidad']); ?></td>
                        <td class="text-end">S/ <?php echo e(number_format($data['igv'], 2)); ?></td>
                        <td class="text-end fw-semibold">S/ <?php echo e(number_format($data['total'], 2)); ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th>TOTAL</th>
                    <th class="text-end"><?php echo e($porMes->sum('cantidad')); ?></th>
                    <th class="text-end">S/ <?php echo e(number_format($porMes->sum('igv'), 2)); ?></th>
                    <th class="text-end">S/ <?php echo e(number_format($porMes->sum('total'), 2)); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/reportes/mensual.blade.php ENDPATH**/ ?>