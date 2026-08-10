@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('header', 'Editar Cliente')

@section('content')
<form action="{{ route('clientes.update', $cliente) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info py-2 mb-3">
                <i class="fas fa-info-circle me-1"></i>
                Si necesitas refrescar los datos desde la API, presiona el botón <strong>Buscar</strong>.
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo Documento</label>
                    <select name="tipo_documento" id="cliente-tipo-doc" class="form-select" required>
                        <option value="DNI" {{ $cliente->tipo_documento == 'DNI' ? 'selected' : '' }}>DNI</option>
                        <option value="RUC" {{ $cliente->tipo_documento == 'RUC' ? 'selected' : '' }}>RUC</option>
                        <option value="CE" {{ $cliente->tipo_documento == 'CE' ? 'selected' : '' }}>Carnet de Extranjería</option>
                        <option value="PASAPORTE" {{ $cliente->tipo_documento == 'PASAPORTE' ? 'selected' : '' }}>Pasaporte</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Número</label>
                    <div class="input-group">
                        <input type="text" name="numero_documento" id="cliente-numero-doc" class="form-control" value="{{ $cliente->numero_documento }}" required>
                        <button type="button" class="btn btn-primary" id="cliente-buscar-decolecta" title="Buscar en la API">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nombre / Razón Social</label>
                    <input type="text" name="nombre_razon_social" id="cliente-nombre" class="form-control" value="{{ $cliente->nombre_razon_social }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="cliente-direccion" class="form-control" value="{{ $cliente->direccion }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ $cliente->telefono }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $cliente->email }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ubigeo</label>
                    <input type="text" name="ubigeo" class="form-control" value="{{ $cliente->ubigeo }}" maxlength="6">
                </div>
            </div>
            <div class="mt-3" id="cliente-consulta-estado" role="status" aria-live="polite"></div>
        </div>
        <div class="card-footer">
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const consultaDocumentoUrl = '{{ route('consulta.documento') }}';
const clienteCsrf = '{{ csrf_token() }}';
const estadoConsulta = document.getElementById('cliente-consulta-estado');

function setEstado(texto, clase) {
    estadoConsulta.innerHTML = texto ? `<div class="alert ${clase} py-2 mb-0">${texto}</div>` : '';
}

function buscarEnDecolecta() {
    const tipo = document.getElementById('cliente-tipo-doc').value;
    const numero = document.getElementById('cliente-numero-doc').value.trim();
    const requerido = tipo === 'DNI' ? 8 : tipo === 'RUC' ? 11 : 0;

    if (!numero) { setEstado('Ingresa el número de documento.', 'alert-warning'); return; }
    if (requerido && numero.length !== requerido) {
        setEstado(`El ${tipo} debe tener ${requerido} dígitos.`, 'alert-warning');
        return;
    }

    setEstado('Buscando datos en la API...', 'alert-info');

    fetch(consultaDocumentoUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': clienteCsrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ tipo, numero }),
    })
    .then(r => r.json().then(j => ({ status: r.status, body: j })))
    .then(({ status, body }) => {
        if (body && body.success) {
            const c = body.cliente;
            document.getElementById('cliente-nombre').value = c.nombre_razon_social || '';
            document.getElementById('cliente-direccion').value = c.direccion || '';
            setEstado(`<i class="fas fa-check-circle"></i> Datos actualizados: <strong>${c.nombre_razon_social}</strong>`, 'alert-success');
        } else {
            setEstado(`<i class="fas fa-exclamation-triangle"></i> ${body.message || 'No se encontraron datos.'}`, 'alert-warning');
        }
    })
    .catch(err => {
        setEstado('<i class="fas fa-times-circle"></i> No se pudo consultar el servicio.', 'alert-danger');
    });
}

document.getElementById('cliente-buscar-decolecta').addEventListener('click', buscarEnDecolecta);
document.getElementById('cliente-numero-doc').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); buscarEnDecolecta(); }
});
</script>
@endpush
