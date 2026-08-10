@extends('layouts.app')

@section('title', 'Arqueo de Caja')
@section('header', 'Arqueo de Caja #' . $caja->id)

@section('content')
<form action="{{ route('arqueo.store', $caja) }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-coins me-2"></i>Conteo por Denominación
                </div>
                <div class="card-body">
                    <p class="text-muted small">Ingresa la cantidad de billetes y monedas contados en caja.</p>
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Denominación</th>
                                <th class="text-center" style="width:120px;">Cantidad</th>
                                <th class="text-end" style="width:150px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($denominaciones as $d)
                                <tr>
                                    <td><strong>S/ {{ number_format($d, 2) }}</strong></td>
                                    <td>
                                        <input type="number" min="0" name="cantidad[{{ $d }}]" class="form-control form-control-sm text-center arqueo-cantidad" data-denom="{{ $d }}" value="0">
                                    </td>
                                    <td class="text-end">
                                        <span class="arqueo-subtotal" data-denom="{{ $d }}">S/ 0.00</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="2" class="text-end">TOTAL CONTADO:</th>
                                <th class="text-end fs-5 text-success" id="arqueo-total">S/ 0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('caja.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Registrar Arqueo y Cerrar Caja
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Información de Caja</div>
                <div class="card-body">
                    <p class="mb-1"><strong>ID Caja:</strong> #{{ $caja->id }}</p>
                    <p class="mb-1"><strong>Cajero:</strong> {{ $caja->usuarioApertura->name ?? '—' }}</p>
                    <p class="mb-1"><strong>Apertura:</strong> {{ $caja->fecha_apertura->format('d/m/Y H:i') }}</p>
                    <p class="mb-1"><strong>Monto apertura:</strong> S/ {{ number_format($caja->monto_apertura, 2) }}</p>
                    <hr>
                    <h6 class="mb-2">Resumen Sistema</h6>
                    <p class="mb-1"><strong>Ventas efectivo:</strong> S/ {{ number_format($caja->total_ventas_efectivo, 2) }}</p>
                    <p class="mb-1"><strong>Ingresos manuales:</strong> S/ {{ number_format($caja->total_ingresos, 2) }}</p>
                    <p class="mb-1"><strong>Egresos manuales:</strong> S/ {{ number_format($caja->total_egresos, 2) }}</p>
                    <hr>
                    <h5 class="text-primary mb-0">Efectivo Teórico: S/ {{ number_format($caja->monto_efectivo_teorico, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('input', (e) => {
    if (e.target.classList.contains('arqueo-cantidad')) {
        const cant = parseInt(e.target.value || 0);
        const denom = parseFloat(e.target.dataset.denom);
        const subtotal = cant * denom;
        e.target.closest('tr').querySelector('.arqueo-subtotal').textContent = 'S/ ' + subtotal.toFixed(2);
        recalcularTotal();
    }
});

function recalcularTotal() {
    let total = 0;
    document.querySelectorAll('.arqueo-cantidad').forEach(inp => {
        const cant = parseInt(inp.value || 0);
        const denom = parseFloat(inp.dataset.denom);
        total += cant * denom;
    });
    document.getElementById('arqueo-total').textContent = 'S/ ' + total.toFixed(2);
}
</script>
@endpush
