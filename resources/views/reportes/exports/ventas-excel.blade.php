<table>
    <tr>
        <td colspan="7" style="font-size:16pt;font-weight:bold;background:#2563eb;color:white;padding:8px;">
            Reporte de Ventas
        </td>
    </tr>
    <tr>
        <td><strong>Desde:</strong></td>
        <td>{{ $filtros['desde'] }}</td>
        <td><strong>Hasta:</strong></td>
        <td>{{ $filtros['hasta'] }}</td>
        <td><strong>Generado:</strong></td>
        <td colspan="2">{{ now()->format('d/m/Y H:i') }}</td>
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
        <td>{{ $totales['cantidad'] }}</td>
        <td>{{ $totales['boletas'] }}</td>
        <td>{{ $totales['facturas'] }}</td>
        <td>S/ {{ number_format($totales['gravadas'], 2) }}</td>
        <td>S/ {{ number_format($totales['exoneradas'], 2) }}</td>
        <td>S/ {{ number_format($totales['igv'], 2) }}</td>
        <td>S/ {{ number_format($totales['total'], 2) }}</td>
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
    @foreach($ventas as $v)
        <tr>
            <td>{{ $v->fecha_emision->format('d/m/Y H:i') }}</td>
            <td>{{ $v->correlativo }} ({{ $v->tipo_comprobante_label }})</td>
            <td>{{ $v->cliente?->nombre_razon_social ?? '—' }}</td>
            <td>{{ $v->usuario?->name ?? '—' }}</td>
            <td>{{ $v->comprobante?->estado ?? '—' }}</td>
            <td>{{ $v->pagos->pluck('metodo_pago')->unique()->implode(', ') ?: '—' }}</td>
            <td>S/ {{ number_format($v->total, 2) }}</td>
        </tr>
    @endforeach
</table>
