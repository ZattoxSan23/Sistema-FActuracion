<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::create([
            'tipo_documento' => Cliente::TIPO_SIN_DOCUMENTO,
            'numero_documento' => '00000000',
            'nombre_razon_social' => 'CLIENTES VARIOS',
            'activo' => true,
        ]);

        $clientes = [
            // Personas naturales (DNI)
            ['tipo_documento' => 'DNI', 'numero_documento' => '45678912', 'nombre_razon_social' => 'Carlos Mendoza Ríos', 'direccion' => 'Calle Las Flores 234, Surco', 'telefono' => '999888777', 'email' => 'carlos.mendoza@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '12345678', 'nombre_razon_social' => 'Juan Pérez García', 'direccion' => 'Av. La Marina 123, San Miguel', 'telefono' => '987654321', 'email' => 'juan.perez@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '78912345', 'nombre_razon_social' => 'Ana Quispe Huamán', 'direccion' => 'Av. Brasil 890, Breña', 'telefono' => '955444333', 'email' => 'ana.quispe@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '32145678', 'nombre_razon_social' => 'Luis Ramírez Castro', 'direccion' => 'Jr. Los Libertadores 456, San Borja', 'telefono' => '988777666', 'email' => 'luis.ramirez@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '56789123', 'nombre_razon_social' => 'Sofía Vargas Mendoza', 'direccion' => 'Av. Pardo y Aliaga 480, San Isidro', 'telefono' => '992111333', 'email' => 'sofia.vargas@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '89123456', 'nombre_razon_social' => 'Diego Huamán Ríos', 'direccion' => 'Calle Schell 120, Miraflores', 'telefono' => '998555111', 'email' => 'diego.huaman@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '23456789', 'nombre_razon_social' => 'Valeria Castillo Vega', 'direccion' => 'Av. Benavides 2345, Miraflores', 'telefono' => '987888222', 'email' => 'valeria.castillo@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '34567890', 'nombre_razon_social' => 'Andrés Rojas Quispe', 'direccion' => 'Jr. Camaná 678, Lima Cercado', 'telefono' => '996333111', 'email' => 'andres.rojas@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '67890123', 'nombre_razon_social' => 'Camila Torres Ríos', 'direccion' => 'Av. Javier Prado Este 1500, La Molina', 'telefono' => '993888555', 'email' => 'camila.torres@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '90123456', 'nombre_razon_social' => 'Mateo Salazar Chávez', 'direccion' => 'Calle Las Begonias 345, San Isidro', 'telefono' => '991222777', 'email' => 'mateo.salazar@gmail.com'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '11223344', 'nombre_razon_social' => 'Luciana Mendoza Torres', 'direccion' => 'Av. Angamos Este 1200, Surquillo', 'telefono' => '989333444', 'email' => 'luciana.mendoza@gmail.com'],

            // Empresas (RUC)
            ['tipo_documento' => 'RUC', 'numero_documento' => '20123456789', 'nombre_razon_social' => 'GRUPO ANDINO S.A.C.', 'direccion' => 'Av. Industrial 789, Ate', 'telefono' => '01-3456789', 'email' => 'contacto@grupoandino.com'],
            ['tipo_documento' => 'RUC', 'numero_documento' => '20987654321', 'nombre_razon_social' => 'COMERCIAL MIRAFLORES E.I.R.L.', 'direccion' => 'Av. Javier Prado 567, San Isidro', 'telefono' => '01-2223333', 'email' => 'ventas@cmiraflores.com'],
            ['tipo_documento' => 'RUC', 'numero_documento' => '20456789012', 'nombre_razon_social' => 'INVERSIONES CAFETALES S.A.', 'direccion' => 'Av. El Derby 055, Surco', 'telefono' => '01-4567890', 'email' => 'info@invcafetales.pe'],
            ['tipo_documento' => 'RUC', 'numero_documento' => '20234567890', 'nombre_razon_social' => 'TECNOLOGIA LIMA S.A.C.', 'direccion' => 'Av. República de Panamá 450, Surquillo', 'telefono' => '01-2345678', 'email' => 'contabilidad@tecnolima.pe'],
            ['tipo_documento' => 'RUC', 'numero_documento' => '20567890123', 'nombre_razon_social' => 'DISTRIBUIDORA ANDINA S.R.L.', 'direccion' => 'Calle Los Negocios 280, Surco', 'telefono' => '01-3334455', 'email' => 'compras@distriandina.pe'],
            ['tipo_documento' => 'RUC', 'numero_documento' => '20345678901', 'nombre_razon_social' => 'CONSULTORIA EMPRESARIAL E.I.R.L.', 'direccion' => 'Av. Javier Prado Oeste 789, Magdalena', 'telefono' => '01-5556677', 'email' => 'gerencia@consulemp.pe'],
            ['tipo_documento' => 'RUC', 'numero_documento' => '20678901234', 'nombre_razon_social' => 'AGENCIA DE MARKETING S.A.C.', 'direccion' => 'Av. Camino Real 250, San Isidro', 'telefono' => '01-7778899', 'email' => 'finanzas@agmarket.pe'],
            ['tipo_documento' => 'RUC', 'numero_documento' => '20789012345', 'nombre_razon_social' => 'RESTAURANTE SABOR ANDINO S.A.C.', 'direccion' => 'Av. La Marina 1500, Pueblo Libre', 'telefono' => '01-4445566', 'email' => 'compras@saborandino.pe'],
        ];

        foreach ($clientes as $c) {
            $c['activo'] = true;
            Cliente::create($c);
        }
    }
}
