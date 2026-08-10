@extends('layouts.app')

@section('title', 'Venta ' . $venta->correlativo)
@section('header', 'Detalle de Venta')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-receipt me-2"></i>{{ $venta->correlativo }}</h2>
    <div>
        <a href="{{ route('ventas.pdf', $venta) }}" target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-file-pdf me-1"></i>PDF
        </a>
        @if($venta->comprobante && $venta->comprobante->xml_firmado)
            <a href="{{ route('ventas.xml', $venta) }}" class="btn btn-outline-secondary">
                <i class="fas fa-code me-1"></i>XML
            </a>
        @endif
        <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Información General</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Tipo:</strong> {{ $venta->tipo_comprobante_label }}<br>
                        <strong>Fecha:</strong> {{ $venta->fecha_emision->format('d/m/Y H:i:s') }}<br>
                        <strong>Vendedor:</strong> {{ $venta->usuario->name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Cliente:</strong> {{ $venta->cliente?->nombre_razon_social ?? '—' }}<br>
                        <strong>Documento:</strong> {{ $venta->cliente?->documento_completo ?? '—' }}<br>
                        <strong>Estado:</strong>
                        @if($venta->estado === 'anulada')
                            <span class="badge bg-dark">Anulada</span>
                        @else
                            <span class="badge bg-success">Activa</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Items</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Cant.</th>
                            <th class="text-end">P. Unit</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">IGV</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->items as $item)
                            <tr>
                                <td>{{ $item->orden }}</td>
                                <td>
                                    {{ $item->descripcion }}
                                    <br><small class="text-muted">{{ $item->codigo_producto }}</small>
                                </td>
                                <td>{{ number_format($item->cantidad, 3) }}</td>
                                <td class="text-end">{{ number_format($item->precio_unitario, 4) }}</td>
                                <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                <td class="text-end">{{ number_format($item->igv_item, 2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($item->total_item, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end">Op. Gravadas:</td>
                            <td class="text-end">S/ {{ number_format($venta->op_gravadas, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end">Op. Exoneradas:</td>
                            <td class="text-end">S/ {{ number_format($venta->op_exoneradas, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end">IGV (18%):</td>
                            <td class="text-end">S/ {{ number_format($venta->igv, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end fw-bold">TOTAL:</td>
                            <td class="text-end fw-bold text-success">S/ {{ number_format($venta->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Pagos</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Método</th>
                            <th>Tipo</th>
                            <th>Operación</th>
                            <th class="text-end">Recibido</th>
                            <th class="text-end">Vuelto</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->pagos as $pago)
                            <tr>
                                <td>{{ $pago->metodo_label }}</td>
                                <td>{{ ucfirst($pago->tipo_tarjeta ?? '—') }}</td>
                                <td>{{ $pago->numero_operacion ?? '—' }}</td>
                                <td class="text-end">{{ $pago->monto_recibido ? 'S/ ' . number_format($pago->monto_recibido, 2) : '—' }}</td>
                                <td class="text-end">{{ $pago->vuelto > 0 ? 'S/ ' . number_format($pago->vuelto, 2) : '—' }}</td>
                                <td class="text-end fw-semibold">S/ {{ number_format($pago->monto, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($venta->comprobante)
            <div class="card mb-3">
                <div class="card-header">Estado SUNAT</div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @php
                            $estados = [
                                'borrador' => ['secondary', 'Borrador'],
                                'firmado' => ['info', 'Firmado'],
                                'enviado' => ['primary', 'Enviado'],
                                'aceptado' => ['success', 'Aceptado'],
                                'rechazado' => ['danger', 'Rechazado'],
                                'excepcion' => ['warning', 'Excepción'],
                                'anulado' => ['dark', 'Anulado'],
                            ];
                            $est = $estados[$venta->comprobante->estado] ?? ['secondary', ucfirst($venta->comprobante->estado)];
                        @endphp
                        <span class="badge bg-{{ $est[0] }} fs-6">{{ $est[1] }}</span>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <td>Ticket:</td>
                            <td class="text-end"><code>{{ $venta->comprobante->ticket ?? '—' }}</code></td>
                        </tr>
                        <tr>
                            <td>Código:</td>
                            <td class="text-end">{{ $venta->comprobante->codigo_respuesta ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td>Hash CPE:</td>
                            <td class="text-end" style="font-size: 0.7rem; word-break: break-all;">{{ $venta->comprobante->hash_cpe ?? '—' }}</td>
                        </tr>
                    </table>
                    @if(in_array($venta->comprobante->estado, ['rechazado', 'excepcion']))
                        <form action="{{ route('ventas.reenviar.sunat', $venta) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-redo me-1"></i>Reenviar a SUNAT
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        @if($venta->estado !== 'anulada' && auth()->user()->isAdmin())
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">Zona peligrosa</div>
                <div class="card-body">
                    <button type="button" class="btn btn-danger w-100" onclick="confirmarAnulacion()">
                        <i class="fas fa-ban me-1"></i>Anular Venta
                    </button>
                </div>
            </div>
        @endif

        @if($venta->motivo_anulacion)
            <div class="card mt-3">
                <div class="card-header">Motivo de Anulación</div>
                <div class="card-body">
                    {{ $venta->motivo_anulacion }}<br>
                    <small class="text-muted">
                        Anulado por {{ $venta->usuarioAnulacion?->name }}
                        el {{ $venta->fecha_anulacion?->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarAnulacion() {
    Swal.fire({
        title: '¿Anular venta?',
        input: 'textarea',
        inputLabel: 'Motivo de anulación',
        inputPlaceholder: 'Describe el motivo...',
        inputAttributes: { required: true },
        showCancelButton: true,
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        preConfirm: (motivo) => {
            if (!motivo || motivo.length < 5) {
                Swal.showValidationMessage('El motivo debe tener al menos 5 caracteres');
                return false;
            }
            return motivo;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('ventas.anular', $venta) }}';
            form.innerHTML = `@csrf<input name="motivo_anulacion" value="${result.value}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
