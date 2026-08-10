<?php

namespace Database\Seeders;

use App\Models\Serie;
use Illuminate\Database\Seeder;

class SerieSeeder extends Seeder
{
    public function run(): void
    {
        $series = [
            ['tipo_comprobante' => Serie::TIPO_BOLETA, 'serie' => 'B001', 'correlativo_actual' => 0, 'principal' => true],
            ['tipo_comprobante' => Serie::TIPO_FACTURA, 'serie' => 'F001', 'correlativo_actual' => 0, 'principal' => true],
            ['tipo_comprobante' => Serie::TIPO_NOTA_CREDITO, 'serie' => 'BC01', 'correlativo_actual' => 0],
            ['tipo_comprobante' => Serie::TIPO_NOTA_DEBITO, 'serie' => 'BD01', 'correlativo_actual' => 0],
        ];

        foreach ($series as $s) {
            $s['correlativo_desde'] = 1;
            $s['correlativo_hasta'] = 99999999;
            $s['activo'] = true;
            Serie::create($s);
        }
    }
}
