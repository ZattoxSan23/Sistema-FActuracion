<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Serie;
use App\Models\SunatConfig;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EmpresaSeeder::class,
            UsuarioSeeder::class,
            CategoriaSeeder::class,
            ProductoSeeder::class,
            SerieSeeder::class,
            ClienteSeeder::class,
            SunatConfigSeeder::class,
            VentaSeeder::class,
            CajaSeeder::class,
        ]);
    }
}
