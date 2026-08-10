<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\User;
use App\Policies\ClientePolicy;
use App\Policies\ProductoPolicy;
use App\Policies\VentaPolicy;
use App\Policies\CajaPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Cliente::class => ClientePolicy::class,
        Producto::class => ProductoPolicy::class,
        Venta::class => VentaPolicy::class,
        Caja::class => CajaPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gates administrativos
        Gate::define('admin-only', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('admin-contador', function ($user) {
            return $user->isAdmin() || $user->isContador();
        });

        Gate::define('admin-cajera', function ($user) {
            return $user->isAdmin() || $user->isCajera();
        });

        Gate::define('reportes', function ($user) {
            return $user->isAdmin() || $user->isContador();
        });
    }
}
