<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::create([
            'ruc' => '20601234567',
            'razon_social' => 'CAFETERIA ANDINA S.A.C.',
            'nombre_comercial' => 'Café Andino',
            'direccion' => 'Av. Larco 345, Miraflores - Lima',
            'ubigeo' => '150122',
            'departamento' => 'LIMA',
            'provincia' => 'LIMA',
            'distrito' => 'MIRAFLORES',
            'telefono' => '(01) 234-5678',
            'email' => 'ventas@cafeandino.pe',
            'web' => 'https://cafeandino.pe',
            'igv' => 18.00,
            'moneda' => 'PEN',
            'tipo_precio' => 'incluye_igv',
            'pie_pagina_ticket' => '¡Gracias por su visita! Vuelva pronto.',
            'mensaje_personalizado' => 'Café 100% orgánico de los Andes peruanos',
            'activo' => true,
        ]);
    }
}
