<?php

namespace App\Console;

use App\Console\Commands\ReenviarComprobantes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ReenviarComprobantes::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Reenviar comprobantes con excepciones cada 30 minutos
        $schedule->command('sunat:reenviar')
            ->everyThirtyMinutes()
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
