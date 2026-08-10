<?php

namespace App\Console\Commands;

use App\Models\Comprobante;
use App\Services\Sunat\SunatService;
use Illuminate\Console\Command;

class ReenviarComprobantes extends Command
{
    protected $signature = 'sunat:reenviar {--comprobante= : ID específico del comprobante}';
    protected $description = 'Reintenta el envío de comprobantes pendientes a SUNAT';

    public function handle(SunatService $sunat): int
    {
        $query = Comprobante::whereIn('estado', ['firmado', 'rechazado', 'excepcion']);

        if ($id = $this->option('comprobante')) {
            $query->where('id', $id);
        }

        $count = 0;
        foreach ($query->get() as $comp) {
            try {
                $this->info("Reenviando comprobante {$comp->correlativo_completo}...");
                $sunat->reenviar($comp);
                $count++;
                $this->info("  ✓ OK");
            } catch (\Exception $e) {
                $this->error("  ✗ Error: {$e->getMessage()}");
            }
        }

        $this->info("Total: {$count} comprobantes reenviados");
        return 0;
    }
}
