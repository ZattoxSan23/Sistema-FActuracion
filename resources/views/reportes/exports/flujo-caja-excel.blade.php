<table>
    <tr>
        <td colspan="4" style="font-size:16pt;font-weight:bold;background:#2563eb;color:white;padding:8px;">
            Flujo de Caja
        </td>
    </tr>
    <tr>
        <td><strong>Desde:</strong></td>
        <td>{{ $filtros['desde'] }}</td>
        <td><strong>Hasta:</strong></td>
        <td>{{ $filtros['hasta'] }}</td>
    </tr>
    <tr><td colspan="4"></td></tr>
    <tr style="background:#10b981;color:white;font-weight:bold;">
        <td colspan="3" class="text-right">Total Ingresos</td>
        <td>S/ {{ number_format($totales['ingresos'], 2) }}</td>
    </tr>
    <tr style="background:#ef4444;color:white;font-weight:bold;">
        <td colspan="3" class="text-right">Total Egresos</td>
        <td>S/ {{ number_format($totales['egresos'], 2) }}</td>
    </tr>
    <tr style="background:#3b82f6;color:white;font-weight:bold;">
        <td colspan="3" class="text-right">Flujo Neto</td>
        <td>S/ {{ number_format($totales['neto'], 2) }}</td>
    </tr>
    <tr><td colspan="4"></td></tr>
    <tr style="background:#1e40af;color:white;font-weight:bold;">
        <td>Fecha</td>
        <td>Ingresos</td>
        <td>Egresos</td>
        <td>Neto</td>
    </tr>
    @foreach($datos as $d)
        <tr>
            <td>{{ $d['fecha'] }}</td>
            <td>S/ {{ number_format($d['ingresos'], 2) }}</td>
            <td>S/ {{ number_format($d['egresos'], 2) }}</td>
            <td>S/ {{ number_format($d['neto'], 2) }}</td>
        </tr>
    @endforeach
</table>
