@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('header', 'Editar: ' . $user->name)

@section('content')
<form action="{{ route('usuarios.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="cajera" {{ $user->rol == 'cajera' ? 'selected' : '' }}>Cajera</option>
                        <option value="contador" {{ $user->rol == 'contador' ? 'selected' : '' }}>Contador</option>
                        <option value="administrador" {{ $user->rol == 'administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-control" value="{{ $user->dni }}" maxlength="15">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ $user->telefono }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ $user->direccion }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Nueva contraseña (dejar vacío para no cambiar)</label>
                    <input type="password" name="password" class="form-control" minlength="6">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </div>
</form>
@endsection
