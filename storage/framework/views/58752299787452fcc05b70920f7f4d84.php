<table>
    <tr>
        <td colspan="7" style="font-size:16pt;font-weight:bold;background:#2563eb;color:white;padding:8px;">
            Reporte de Ventas
        </td>
    </tr>
    <tr>
        <td><strong>Desde:</strong></td>
        <td><?php echo e($filtros['desde']); ?></td>
        <td><strong>Hasta:</strong></td>
        <td><?php echo e($filtros['hasta']); ?></td>
        <td><strong>Generado:</strong></td>
        <td colspan="2"><?php echo e(now()->format('d/m/Y H:i')); ?></td>
    </tr>
    <tr><td colspan="7"></td></tr>
    <tr style="background:#f1f5f9;font-weight:bold;">
        <td>Total Ventas</td>
        <td>Boletas</td>
        <td>Facturas</td>
        <td>Op. Gravadas</td>
        <td>Op. Exoneradas</td>
        <td>IGV</td>
        <td>Total</td>
    </tr>
    <tr style="font-weight:bold;">
        <td><?php echo e($totales['cantidad']); ?></td>
        <td><?php echo e($totales['boletas']); ?></td>
        <td><?php echo e($totales['facturas']); ?></td>
        <td>S/ <?php echo e(number_format($totales['gravadas'], 2)); ?></td>
        <td>S/ <?php echo e(number_format($totales['exoneradas'], 2)); ?></td>
        <td>S/ <?php echo e(number_format($totales['igv'], 2)); ?></td>
        <td>S/ <?php echo e(number_format($totales['total'], 2)); ?></td>
    </tr>
    <tr><td colspan="7"></td></tr>
    <tr style="background:#2563eb;color:white;font-weight:bold;">
        <td>Fecha</td>
        <td>Comprobante</td>
        <td>Cliente</td>
        <td>Vendedor</td>
        <td>Estado SUNAT</td>
        <td>Métodos</td>
        <td>Total</td>
    </tr>
    <?php $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($v->fecha_emision->format('d/m/Y H:i')); ?></td>
            <td><?php echo e($v->correlativo); ?> (<?php echo e($v->tipo_comprobante_label); ?>)</td>
            <td><?php echo e($v->cliente?->nombre_razon_social ?? '—'); ?></td>
            <td><?php echo e($v->usuario?->name ?? '—'); ?></td>
            <td><?php echo e($v->comprobante?->estado ?? '—'); ?></td>
            <td><?php echo e($v->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: '—'); ?></td>
            <td>S/ <?php echo e(number_format($v->total, 2)); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>
<?php /**PATH /var/www/html/resources/views/reportes/exports/ventas-excel.blade.php ENDPATH**/ ?>