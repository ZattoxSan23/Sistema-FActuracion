@extends('layouts.app')

@section('title', 'Nuevo Usuario')
@section('header', 'Nuevo Usuario')

@section('content')
<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="cajera">Cajera</option>
                        <option value="contador">Contador</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-control" maxlength="15">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" minlength="6" required>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </div>
    </div>
</form>
@endsection
