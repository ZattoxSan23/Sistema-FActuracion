<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $precio = $this->faker->randomFloat(2, 5, 100);
        return [
            'categoria_id' => Categoria::inRandomOrder()->first()?->id,
            'codigo' => strtoupper($this->faker->unique()->bothify('???###')),
            'codigo_barra' => $this->faker->unique()->numerify('775##########'),
            'nombre' => ucfirst($this->faker->words(3, true)),
            'descripcion' => $this->faker->sentence(),
            'unidad_medida' => 'NIU',
            'precio_compra' => round($precio * 0.7, 2),
            'precio_venta' => $precio,
            'precio_mayorista' => round($precio * 0.85, 2),
            'tipo_afectacion_igv' => Producto::AFECT_GRAVADO,
            'incluye_igv' => true,
            'activo' => true,
            'visible_pos' => true,
            'orden' => 0,
        ];
    }
}
