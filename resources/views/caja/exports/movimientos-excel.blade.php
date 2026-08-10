<table>
    <tr>
        <td colspan="7" style="font-size:16pt;font-weight:bold;background:#2563eb;color:white;padding:8px;">
            Caja #{{ $caja->id }} - Movimientos
        </td>
    </tr>
    <tr>
        <td><strong>Cajero:</strong></td>
        <td>{{ $caja->usuarioApertura->name ?? '—' }}</td>
        <td><strong>Estado:</strong></td>
        <td>{{ strtoupper($caja->estado) }}</td>
        <td><strong>Apertura:</strong></td>
        <td>{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</td>
    </tr>
    <tr><td colspan="7"></td></tr>
    <tr style="background:#f1f5f9;font-weight:bold;">
        <td>Monto Apertura</td>
        <td>Ventas Efectivo</td>
        <td>Ingresos</td>
        <td>Egresos</td>
        <td>Teórico</td>
        <td>Real</td>
        <td>Diferencia</td>
    </tr>
    <tr>
        <td>S/ {{ number_format($caja->monto_apertura, 2) }}</td>
        <td>S/ {{ number_format($caja->total_ventas_efectivo, 2) }}</td>
        <td>S/ {{ number_format($caja->total_ingresos, 2) }}</td>
        <td>S/ {{ number_format($caja->total_egresos, 2) }}</td>
        <td>S/ {{ number_format($caja->monto_efectivo_teorico, 2) }}</td>
        <td>{{ $caja->monto_efectivo_real ? 'S/ '.number_format($caja->monto_efectivo_real, 2) : '—' }}</td>
        <td>{{ $caja->diferencia ? 'S/ '.number_format($caja->diferencia, 2) : '—' }}</td>
    </tr>
    <tr><td colspan="7"></td></tr>
    <tr style="background:#1e40af;color:white;font-weight:bold;">
        <td>Fecha</td>
        <td>Tipo</td>
        <td>Método</td>
        <td>Concepto</td>
        <td>Referencia</td>
        <td>Usuario</td>
        <td>Monto</td>
    </tr>
    @foreach($caja->movimientos as $m)
        <tr>
            <td>{{ $m->fecha->format('d/m/Y H:i') }}</td>
            <td>{{ ucfirst($m->tipo) }}</td>
            <td>{{ ucfirst($m->metodo_pago) }}</td>
            <td>{{ $m->concepto }}</td>
            <td>{{ $m->referencia ?? '—' }}</td>
            <td>{{ $m->usuario->name ?? '—' }}</td>
            <td>S/ {{ number_format($m->monto, 2) }}</td>
        </tr>
    @endforeach
</table>
