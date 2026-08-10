@php
    $user = auth()->user();
    $cajaAbierta = \App\Models\Caja::cajaAbierta();
@endphp

<header class="header">
    <h1>@yield('header', 'Dashboard')</h1>

    <div class="ms-auto d-flex align-items-center gap-3">
        @if($cajaAbierta && ($user->isAdmin() || $user->isCajera()))
            <div class="badge bg-success-subtle text-success p-2">
                <i class="fas fa-cash-register me-1"></i>
                Caja abierta
                @if($cajaAbierta->user_id_apertura === $user->id)
                    (Tuya)
                @else
                    ({{ $cajaAbierta->usuarioApertura->name ?? '' }})
                @endif
            </div>
        @endif

        <div class="dropdown">
            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div class="text-start d-none d-md-block">
                    <div class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ $user->name }}</div>
                    <small class="text-muted text-capitalize">{{ $user->rol }}</small>
                </div>
                <i class="fas fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('perfil.edit') }}"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
