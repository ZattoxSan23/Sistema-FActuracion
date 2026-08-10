<?php

namespace App\Providers;

use App\Services\Printer\PrinterService;
use Illuminate\Support\ServiceProvider;

class PrinterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PrinterService::class, function ($app) {
            return new PrinterService();
        });
    }

    public function boot(): void
    {
        //
    }
}
