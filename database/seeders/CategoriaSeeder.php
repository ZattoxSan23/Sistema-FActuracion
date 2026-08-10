<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Cafés Calientes', 'color' => '#6f4e37', 'icono' => 'fas fa-mug-hot', 'orden' => 1],
            ['nombre' => 'Bebidas Frías', 'color' => '#3b82f6', 'icono' => 'fas fa-glass-whiskey', 'orden' => 2],
            ['nombre' => 'Frappé y Smoothies', 'color' => '#8b5cf6', 'icono' => 'fas fa-blender', 'orden' => 3],
            ['nombre' => 'Tés e Infusiones', 'color' => '#10b981', 'icono' => 'fas fa-leaf', 'orden' => 4],
            ['nombre' => 'Panadería y Pastelería', 'color' => '#f59e0b', 'icono' => 'fas fa-bread-slice', 'orden' => 5],
            ['nombre' => 'Desayunos y Almuerzos', 'color' => '#ef4444', 'icono' => 'fas fa-utensils', 'orden' => 6],
            ['nombre' => 'Postres', 'color' => '#ec4899', 'icono' => 'fas fa-ice-cream', 'orden' => 7],
            ['nombre' => 'Snacks y Dulces', 'color' => '#06b6d4', 'icono' => 'fas fa-cookie', 'orden' => 8],
            ['nombre' => 'Granos y Merchandising', 'color' => '#a16207', 'icono' => 'fas fa-shopping-bag', 'orden' => 9],
        ];

        foreach ($categorias as $cat) {
            Categoria::create($cat);
        }
    }
}
