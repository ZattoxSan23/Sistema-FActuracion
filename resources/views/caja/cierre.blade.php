@extends('layouts.app')

@section('title', 'Cierre de Caja')
@section('header', 'Cerrar Caja #' . $caja->id)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-lock me-2"></i>Cuadre de Caja
            </div>
            <div class="card-body">
                <form action="{{ route('caja.cierre.store', $caja) }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Monto Apertura</label>
                            <input type="text" class="form-control text-end" value="S/ {{ number_format($caja->monto_apertura, 2) }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Efectivo Teórico</label>
                            <input type="text" id="efectivo-teorico" class="form-control text-end fw-bold fs-5 text-primary" value="S/ {{ number_format($caja->monto_efectivo_teorico, 2) }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="monto_efectivo_real" class="form-label fw-bold">
                            Efectivo Real en Caja (conteo físico)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" step="0.01" min="0" name="monto_efectivo_real" id="monto_efectivo_real"
                                   class="form-control form-control-lg text-end @error('monto_efectivo_real') is-invalid @enderror"
                                   value="{{ old('monto_efectivo_real', $caja->monto_efectivo_teorico) }}" required
                                   oninput="calcularDiferencia()">
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between">
                            <span>Diferencia:</span>
                            <strong id="diferencia" class="fs-5">S/ 0.00</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones_cierre" class="form-label">Observaciones</label>
                        <textarea name="observaciones_cierre" id="observaciones_cierre" rows="3"
                                  class="form-control"
                                  placeholder="Notas sobre el cierre (si hay diferencia, indicar motivo)...">{{ old('observaciones_cierre') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('caja.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-danger flex-grow-1" onclick="return confirmarCierre()">
                            <i class="fas fa-lock me-2"></i>CERRAR CAJA
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Movimientos de la Caja</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Hora</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Método</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($caja->movimientos as $mov)
                                <tr>
                                    <td>{{ $mov->fecha->format('H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $mov->tipo === 'ingreso' ? 'success' : 'danger' }}-subtle text-{{ $mov->tipo === 'ingreso' ? 'success' : 'danger' }}">
                                            {{ $mov->tipo_label }}
                                        </span>
                                    </td>
                                    <td>{{ $mov->concepto }}</td>
                                    <td>{{ ucfirst($mov->metodo_pago) }}</td>
                                    <td class="text-end fw-semibold">S/ {{ number_format($mov->monto, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin movimientos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Resumen de la Caja</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td>Ventas realizadas:</td>
                        <td class="text-end fw-semibold">{{ $caja->cantidad_ventas }}</td>
                    </tr>
                    <tr>
                        <td>Efectivo (ventas):</td>
                        <td class="text-end">S/ {{ number_format($caja->total_ventas_efectivo, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tarjeta:</td>
                        <td class="text-end">S/ {{ number_format($caja->total_ventas_tarjeta, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Yape/Plin:</td>
                        <td class="text-end">S/ {{ number_format($caja->total_ventas_yape, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Transferencia:</td>
                        <td class="text-end">S/ {{ number_format($caja->total_ventas_transferencia, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td>Ingresos manuales:</td>
                        <td class="text-end text-success">+ S/ {{ number_format($caja->total_ingresos, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td>Egresos manuales:</td>
                        <td class="text-end text-danger">- S/ {{ number_format($caja->total_egresos, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Movimiento Rápido</div>
            <div class="card-body">
                <form action="{{ route('caja.movimiento.rapido') }}" method="POST">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <select name="tipo" class="form-select form-select-sm" required>
                                <option value="ingreso">Ingreso</option>
                                <option value="egreso">Egreso</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="number" name="monto" class="form-control form-control-sm text-end" placeholder="Monto" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <input type="text" name="concepto" class="form-control form-control-sm mb-2" placeholder="Concepto" required>
                    <select name="metodo_pago" class="form-select form-select-sm mb-2" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="yape">Yape</option>
                        <option value="plin">Plin</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-plus me-1"></i>Registrar Movimiento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const efectivoTeorico = {{ (float) $caja->monto_efectivo_teorico }};

function calcularDiferencia() {
    const real = parseFloat(document.getElementById('monto_efectivo_real').value) || 0;
    const diff = real - efectivoTeorico;
    const el = document.getElementById('diferencia');
    el.textContent = 'S/ ' + diff.toFixed(2);
    el.className = diff === 0 ? 'fs-5 text-success' : (diff > 0 ? 'fs-5 text-success' : 'fs-5 text-danger');
}

function confirmarCierre() {
    const real = parseFloat(document.getElementById('monto_efectivo_real').value) || 0;
    const diff = real - efectivoTeorico;
    let msg = '¿Confirmar cierre de caja con efectivo real de S/ ' + real.toFixed(2) + '?';
    if (Math.abs(diff) > 0.01) {
        msg += '\n\nHay una diferencia de S/ ' + diff.toFixed(2);
    }
    return confirm(msg);
}

calcularDiferencia();
</script>
@endpush
