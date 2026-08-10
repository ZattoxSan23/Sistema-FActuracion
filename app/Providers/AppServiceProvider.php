<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Timezone por defecto
        date_default_timezone_set(config('app.timezone', 'America/Lima'));

        // Vite para assets
        Vite::useScriptTagAttributes([
            'data-turbo-track' => 'reload',
        ]);
    }
}
