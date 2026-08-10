@extends('layouts.app')

@section('title', 'Configuración SUNAT')
@section('header', 'Configuración SUNAT')

@section('content')
<form action="{{ route('sunat.configuracion.guardar') }}" method="POST">
    @csrf
    <div class="card mb-3">
        <div class="card-header bg-warning text-dark">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Importante:</strong> Estos datos son necesarios para emisión de comprobantes electrónicos.
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Entorno</label>
                    <select name="entorno" class="form-select" required>
                        <option value="beta" {{ ($config->entorno ?? 'beta') == 'beta' ? 'selected' : '' }}>Beta (Pruebas)</option>
                        <option value="produccion" {{ ($config->entorno ?? '') == 'produccion' ? 'selected' : '' }}>Producción</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Modo de Envío</label>
                    <select name="modo_envio" class="form-select" required>
                        <option value="ose" {{ ($config->modo_envio ?? 'ose') == 'ose' ? 'selected' : '' }}>OSE (Operador de Servicios)</option>
                        <option value="gre" {{ ($config->modo_envio ?? '') == 'gre' ? 'selected' : '' }}>GRE (Guía Remisión Electrónica - SOAP)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL GRE (SOAP)</label>
                    <input type="url" name="gre_url" class="form-control" value="{{ $config->gre_url ?? 'https://gre-test.nubefact.com/ol-ti-itcpe/billService' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL OSE (REST)</label>
                    <input type="url" name="ose_url" class="form-control" value="{{ $config->ose_url ?? 'https://ose-test.nubefact.com/api/v1' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Usuario SOL</label>
                    <input type="text" name="usuario_sol" class="form-control" value="{{ $config->usuario_sol }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Clave SOL</label>
                    <input type="password" name="clave_sol" class="form-control" value="{{ $config->clave_sol }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contraseña Certificado (.pfx)</label>
                    <input type="password" name="certificado_password" class="form-control" placeholder="Dejar vacío para mantener la actual">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Intentos máximos</label>
                    <input type="number" name="intentos_max" class="form-control" min="1" max="10" value="{{ $config->intentos_max ?? 3 }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Timeout (seg)</label>
                    <input type="number" name="timeout_segundos" class="form-control" min="10" max="300" value="{{ $config->timeout_segundos ?? 30 }}" required>
                </div>
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="envio_automatico" value="1" class="form-check-input" id="envio_auto" {{ ($config->envio_automatico ?? true) ? 'checked' : '' }}>
                        <label for="envio_auto" class="form-check-label">Enviar automáticamente a SUNAT al registrar la venta</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="2">{{ $config->notas }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Guardar Configuración
            </button>
            <button type="button" class="btn btn-outline-success" onclick="probarConexion()">
                <i class="fas fa-plug me-1"></i>Probar Conexión
            </button>
            <div id="resultado-conexion" class="mt-3"></div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <i class="fas fa-shield-alt me-2"></i>Acciones SUNAT
    </div>
    <div class="card-body">
        <a href="{{ route('sunat.comprobantes') }}" class="btn btn-outline-primary">
            <i class="fas fa-file-alt me-1"></i>Ver Comprobantes Enviados
        </a>
        <a href="{{ route('sunat.respuestas') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list me-1"></i>Ver Log de Respuestas
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function probarConexion() {
    const div = document.getElementById('resultado-conexion');
    div.innerHTML = '<div class="alert alert-info">Probando conexión...</div>';

    fetch('{{ route('sunat.probar.conexion') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            div.innerHTML = `<div class="alert alert-success">✓ Conexión exitosa (HTTP ${data.http_status})</div>`;
        } else {
            div.innerHTML = `<div class="alert alert-danger">✗ Error: ${data.error || data.message || 'No se pudo conectar'}</div>`;
        }
    })
    .catch(err => {
        div.innerHTML = `<div class="alert alert-danger">✗ ${err.message}</div>`;
    });
}
</script>
@endpush
