<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'tipo_documento' => 'DNI',
            'numero_documento' => $this->faker->unique()->numerify('########'),
            'nombre_razon_social' => $this->faker->name(),
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->numerify('9########'),
            'email' => $this->faker->safeEmail(),
            'activo' => true,
        ];
    }
}
