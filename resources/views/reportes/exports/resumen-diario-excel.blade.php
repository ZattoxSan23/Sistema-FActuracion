<table>
    <tr>
        <td colspan="4" style="font-size:16pt;font-weight:bold;background:#2563eb;color:white;padding:8px;">
            Resumen Diario - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </td>
    </tr>
    <tr><td colspan="4"></td></tr>
    <tr style="background:#3b82f6;color:white;font-weight:bold;">
        <td>Indicador</td>
        <td colspan="3">Valor</td>
    </tr>
    <tr><td>Ventas del día</td><td colspan="3">{{ $datos['kpis']['ventas_count'] }}</td></tr>
    <tr><td>Total ventas (S/)</td><td colspan="3">S/ {{ number_format($datos['kpis']['total_ventas'], 2) }}</td></tr>
    <tr><td>IGV recaudado (S/)</td><td colspan="3">S/ {{ number_format($datos['kpis']['igv'], 2) }}</td></tr>
    <tr><td>Boletas</td><td colspan="3">{{ $datos['kpis']['boletas'] }}</td></tr>
    <tr><td>Facturas</td><td colspan="3">{{ $datos['kpis']['facturas'] }}</td></tr>
    <tr><td>Egresos caja (S/)</td><td colspan="3">S/ {{ number_format($datos['kpis']['egresos_caja'], 2) }}</td></tr>
    <tr><td>Total egresos (S/)</td><td colspan="3">S/ {{ number_format($datos['kpis']['total_egresos'], 2) }}</td></tr>
    <tr><td>Flujo neto (S/)</td><td colspan="3">S/ {{ number_format($datos['kpis']['neto'], 2) }}</td></tr>
    <tr><td colspan="4"></td></tr>
    <tr style="background:#1e40af;color:white;font-weight:bold;">
        <td colspan="4">Distribución por Método de Pago</td>
    </tr>
    <tr style="background:#dbeafe;font-weight:bold;">
        <td>Método</td>
        <td>Cantidad</td>
        <td colspan="2">Total (S/)</td>
    </tr>
    @foreach($datos['por_metodo'] as $metodo => $info)
        <tr>
            <td>{{ ucfirst($metodo) }}</td>
            <td>{{ $info['cantidad'] }}</td>
            <td colspan="2">S/ {{ number_format($info['total'], 2) }}</td>
        </tr>
    @endforeach
    <tr><td colspan="4"></td></tr>
    <tr style="background:#1e40af;color:white;font-weight:bold;">
        <td colspan="4">Top 10 Productos</td>
    </tr>
    <tr style="background:#dbeafe;font-weight:bold;">
        <td>#</td>
        <td>Producto</td>
        <td>Cantidad</td>
        <td>Total (S/)</td>
    </tr>
    @foreach($datos['top_productos'] as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->nombre }}</td>
            <td>{{ number_format($p->cantidad, 2) }}</td>
            <td>S/ {{ number_format($p->total, 2) }}</td>
        </tr>
    @endforeach
</table>
