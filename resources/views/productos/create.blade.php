@extends('layouts.app')

@section('title', 'Nuevo Producto')
@section('header', 'Nuevo Producto')

@section('content')
<form action="{{ route('productos.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info py-2 mb-3">
                <i class="fas fa-magic me-1"></i>
                El <strong>código</strong> y el <strong>código de barras</strong> se generan automáticamente a partir del nombre del producto.
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Código (automático)</label>
                    <input type="text" id="codigo-preview" class="form-control bg-light" value="Se generará al guardar" readonly>
                    <input type="hidden" name="codigo" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Código de Barras (automático)</label>
                    <input type="text" id="codigo-barra-preview" class="form-control bg-light" value="Se generará al guardar" readonly>
                    <input type="hidden" name="codigo_barra" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion') }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                    <select name="unidad_medida" class="form-select" required>
                        <option value="NIU">NIU - Unidad</option>
                        <option value="KG">KG - Kilogramo</option>
                        <option value="LT">LT - Litro</option>
                        <option value="MTR">MTR - Metro</option>
                        <option value="BX">BX - Caja</option>
                        <option value="DZN">DZN - Docena</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio Compra</label>
                    <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ old('precio_compra', 0) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio Venta <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="precio_venta" class="form-control @error('precio_venta') is-invalid @enderror" value="{{ old('precio_venta') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio Mayorista</label>
                    <input type="number" step="0.01" name="precio_mayorista" class="form-control" value="{{ old('precio_mayorista') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo Afectación IGV</label>
                    <select name="tipo_afectacion_igv" class="form-select">
                        <option value="10">10 - Gravado</option>
                        <option value="20">20 - Exonerado</option>
                        <option value="30">30 - Inafecto</option>
                        <option value="40">40 - Exportación</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stock Inicial</label>
                    <input type="number" step="0.01" min="0" name="stock" class="form-control" value="{{ old('stock', 0) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stock Mínimo</label>
                    <input type="number" step="0.01" min="0" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', 5) }}">
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="incluye_igv" value="1" class="form-check-input" id="incluye_igv" {{ old('incluye_igv', true) ? 'checked' : '' }}>
                        <label for="incluye_igv" class="form-check-label">Precio incluye IGV</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="visible_pos" value="1" class="form-check-input" id="visible_pos" {{ old('visible_pos', true) ? 'checked' : '' }}>
                        <label for="visible_pos" class="form-check-label">Visible en POS</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function quitarTildes(texto) {
    return texto.normalize('NFD').replace(/[̀-ͯ]/g, '');
}

function previsualizarCodigo() {
    const nombre = document.querySelector('[name="nombre"]').value.trim();
    if (!nombre) return;
    const limpio = quitarTildes(nombre).toUpperCase().replace(/[^A-Z0-9]+/g, ' ').trim();
    const palabras = limpio.split(' ').filter(Boolean);
    let ini = palabras.map(p => p.charAt(0)).join('').slice(0, 3);
    if (!ini) ini = 'PRD';
    document.getElementById('codigo-preview').value = ini;
}

document.querySelector('[name="nombre"]').addEventListener('input', previsualizarCodigo);
</script>
@endpush
