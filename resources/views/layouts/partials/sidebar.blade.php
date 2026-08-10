@php
    $user = auth()->user();
    $cajaAbierta = \App\Models\Caja::cajaAbierta();
@endphp

<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>{{ \App\Models\Empresa::actual()?->nombre_comercial ?? 'Facturación' }}</span>
    </div>

    <nav class="py-2">
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if($user->isAdmin() || $user->isCajera())
                <li class="sidebar-section">Operaciones</li>

                <li>
                    <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i>
                        <span>Punto de Venta</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('caja.index') }}" class="{{ request()->routeIs('caja.*') || request()->routeIs('arqueo.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i>
                        <span>Caja</span>
                        @if($cajaAbierta && $user->isCajera())
                            <span class="badge bg-success ms-auto">Abierta</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('ventas.index') }}" class="{{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i>
                        <span>Ventas</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-section">Catálogo</li>

            <li>
                <a href="{{ route('productos.index') }}" class="{{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
            </li>

            @if($user->isAdmin())
                <li>
                    <a href="{{ route('categorias.index') }}" class="{{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        <span>Categorías</span>
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ route('clientes.index') }}" class="{{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>

            @if($user->isAdmin() || $user->isContador())
                <li class="sidebar-section">Reportes</li>

                <li>
                    <a href="{{ route('reportes.index') }}" class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Reportes</span>
                    </a>
                </li>
            @endif

            @if($user->isAdmin())
                <li class="sidebar-section">Administración</li>

                <li>
                    <a href="{{ route('sunat.configuracion') }}" class="{{ request()->routeIs('sunat.*') ? 'active' : '' }}">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>SUNAT</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('configuracion.index') }}" class="{{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Configuración</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-section">Cuenta</li>

            <li>
                <a href="{{ route('perfil.edit') }}" class="{{ request()->routeIs('perfil.*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i>
                    <span>Mi Perfil</span>
                </a>
            </li>

            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </nav>
</aside>
