<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Carlos Mendoza',
                'email' => 'admin@facturacion.local',
                'password' => Hash::make('admin123'),
                'rol' => User::ROL_ADMIN,
                'dni' => '45678912',
                'telefono' => '999888777',
                'direccion' => 'Av. La Marina 123, San Miguel',
                'activo' => true,
            ],
            [
                'name' => 'María López Vargas',
                'email' => 'cajera@facturacion.local',
                'password' => Hash::make('cajera123'),
                'rol' => User::ROL_CAJERA,
                'dni' => '87654321',
                'telefono' => '987123456',
                'direccion' => 'Jr. Los Pinos 456, Miraflores',
                'activo' => true,
            ],
            [
                'name' => 'Ana Quispe Huamán',
                'email' => 'cajera2@facturacion.local',
                'password' => Hash::make('cajera123'),
                'rol' => User::ROL_CAJERA,
                'dni' => '78912345',
                'telefono' => '955444333',
                'direccion' => 'Av. Brasil 890, Breña',
                'activo' => true,
            ],
            [
                'name' => 'Patricia Rojas',
                'email' => 'contador@facturacion.local',
                'password' => Hash::make('contador123'),
                'rol' => User::ROL_CONTADOR,
                'dni' => '12345678',
                'telefono' => '987456789',
                'direccion' => 'Calle Las Flores 234, Surco',
                'activo' => true,
            ],
        ];

        foreach ($usuarios as $data) {
            User::create($data);
        }
    }
}
