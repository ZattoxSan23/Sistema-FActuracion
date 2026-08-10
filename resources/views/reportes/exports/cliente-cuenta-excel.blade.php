<table>
    <tr>
        <td colspan="6" style="font-size:16pt;font-weight:bold;background:#2563eb;color:white;padding:8px;">
            Estado de Cuenta - {{ $cliente->nombre_razon_social }}
        </td>
    </tr>
    <tr>
        <td><strong>Documento:</strong></td>
        <td>{{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}</td>
        <td><strong>Periodo:</strong></td>
        <td>{{ $filtros['desde'] }} a {{ $filtros['hasta'] }}</td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr style="background:#3b82f6;color:white;font-weight:bold;">
        <td colspan="3">Resumen</td>
        <td colspan="3">Valor</td>
    </tr>
    <tr><td colspan="3">Total Compras</td><td colspan="3">S/ {{ number_format($totales['total'], 2) }}</td></tr>
    <tr><td colspan="3">Cantidad</td><td colspan="3">{{ $totales['cantidad'] }}</td></tr>
    <tr><td colspan="3">Boletas</td><td colspan="3">{{ $totales['boletas'] }}</td></tr>
    <tr><td colspan="3">Facturas</td><td colspan="3">{{ $totales['facturas'] }}</td></tr>
    <tr><td colspan="6"></td></tr>
    <tr style="background:#1e40af;color:white;font-weight:bold;">
        <td>Fecha</td>
        <td>Comprobante</td>
        <td>Tipo</td>
        <td>Items</td>
        <td>Estado</td>
        <td>Total (S/)</td>
    </tr>
    @foreach($ventas as $v)
        <tr>
            <td>{{ $v->fecha_emision->format('d/m/Y H:i') }}</td>
            <td>{{ $v->correlativo }}</td>
            <td>{{ $v->tipo_comprobante_label }}</td>
            <td>{{ $v->items->count() }}</td>
            <td>{{ $v->comprobante?->estado ?? '—' }}</td>
            <td>S/ {{ number_format($v->total, 2) }}</td>
        </tr>
    @endforeach
</table>
