<?php

namespace Database\Seeders;

use App\Models\SunatConfig;
use Illuminate\Database\Seeder;

class SunatConfigSeeder extends Seeder
{
    public function run(): void
    {
        SunatConfig::create([
            'entorno' => 'beta',
            'modo_envio' => 'ose',
            'gre_url' => 'https://gre-test.nubefact.com/ol-ti-itcpe/billService',
            'ose_url' => 'https://ose-test.nubefact.com/api/v1',
            'usuario_sol' => 'MODDATOS',
            'clave_sol' => 'moddatos',
            'certificado_path' => null,
            'certificado_password' => null,
            'envio_automatico' => false, // Desactivado por defecto hasta configurar certificado
            'intentos_max' => 3,
            'timeout_segundos' => 30,
            'notas' => 'Configuración inicial - actualizar con datos reales del certificado y credenciales SOL',
        ]);
    }
}
