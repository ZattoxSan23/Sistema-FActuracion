<?php

namespace App\Providers;

use App\Services\Sunat\SunatService;
use Illuminate\Support\ServiceProvider;

class SunatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SunatService::class, function ($app) {
            return new SunatService();
        });
    }

    public function boot(): void
    {
        //
    }
}
