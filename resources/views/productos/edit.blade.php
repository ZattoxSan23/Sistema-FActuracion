@extends('layouts.app')

@section('title', 'Editar Producto')
@section('header', 'Editar: ' . $producto->nombre)

@section('content')
<form action="{{ route('productos.update', $producto) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Código (generado)</label>
                    <input type="text" class="form-control bg-light" value="{{ $producto->codigo }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Código de Barras (generado)</label>
                    <input type="text" class="form-control bg-light" value="{{ $producto->codigo_barra ?? '—' }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unidad</label>
                    <select name="unidad_medida" class="form-select">
                        <option value="NIU" {{ $producto->unidad_medida == 'NIU' ? 'selected' : '' }}>NIU</option>
                        <option value="KG" {{ $producto->unidad_medida == 'KG' ? 'selected' : '' }}>KG</option>
                        <option value="LT" {{ $producto->unidad_medida == 'LT' ? 'selected' : '' }}>LT</option>
                        <option value="MTR" {{ $producto->unidad_medida == 'MTR' ? 'selected' : '' }}>MTR</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio Compra</label>
                    <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ old('precio_compra', $producto->precio_compra) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio Venta</label>
                    <input type="number" step="0.01" name="precio_venta" class="form-control" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Precio Mayorista</label>
                    <input type="number" step="0.01" name="precio_mayorista" class="form-control" value="{{ old('precio_mayorista', $producto->precio_mayorista) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo Afectación IGV</label>
                    <select name="tipo_afectacion_igv" class="form-select">
                        <option value="10" {{ $producto->tipo_afectacion_igv == '10' ? 'selected' : '' }}>Gravado</option>
                        <option value="20" {{ $producto->tipo_afectacion_igv == '20' ? 'selected' : '' }}>Exonerado</option>
                        <option value="30" {{ $producto->tipo_afectacion_igv == '30' ? 'selected' : '' }}>Inafecto</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="incluye_igv" value="1" class="form-check-input" id="incluye_igv" {{ $producto->incluye_igv ? 'checked' : '' }}>
                        <label for="incluye_igv" class="form-check-label">Precio incluye IGV</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo" {{ $producto->activo ? 'checked' : '' }}>
                        <label for="activo" class="form-check-label">Activo</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="visible_pos" value="1" class="form-check-input" id="visible_pos" {{ $producto->visible_pos ? 'checked' : '' }}>
                        <label for="visible_pos" class="form-check-label">POS</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </div>
</form>
@endsection
