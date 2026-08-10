<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Cafés Calientes
            ['codigo' => 'CAP', 'codigo_barra' => '7750001001018', 'nombre' => 'Cappuccino', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 4.20, 'precio_venta' => 12.00, 'stock' => 80, 'stock_minimo' => 20],
            ['codigo' => 'LAM', 'codigo_barra' => '7750001001025', 'nombre' => 'Latte', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 4.20, 'precio_venta' => 12.00, 'stock' => 75, 'stock_minimo' => 20],
            ['codigo' => 'ESP', 'codigo_barra' => '7750001001032', 'nombre' => 'Espresso', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 3.00, 'precio_venta' => 8.00, 'stock' => 100, 'stock_minimo' => 30],
            ['codigo' => 'AMR', 'codigo_barra' => '7750001001049', 'nombre' => 'Americano', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 3.20, 'precio_venta' => 9.00, 'stock' => 90, 'stock_minimo' => 25],
            ['codigo' => 'MOC', 'codigo_barra' => '7750001001056', 'nombre' => 'Mochaccino', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 4.80, 'precio_venta' => 13.50, 'stock' => 60, 'stock_minimo' => 15],
            ['codigo' => 'MAC', 'codigo_barra' => '7750001001063', 'nombre' => 'Macchiato', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 3.60, 'precio_venta' => 10.00, 'stock' => 50, 'stock_minimo' => 15],
            ['codigo' => 'CFA', 'codigo_barra' => '7750001001070', 'nombre' => 'Café con Leche', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 3.40, 'precio_venta' => 9.50, 'stock' => 70, 'stock_minimo' => 20],
            ['codigo' => 'IRP', 'codigo_barra' => '7750001001087', 'nombre' => 'Irlandés', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 6.50, 'precio_venta' => 16.00, 'stock' => 30, 'stock_minimo' => 10],
            ['codigo' => 'PAS', 'codigo_barra' => '7750001001094', 'nombre' => 'Pasión Andina (Especialidad)', 'categoria_id' => 1, 'unidad_medida' => 'NIU', 'precio_compra' => 7.00, 'precio_venta' => 18.00, 'stock' => 25, 'stock_minimo' => 8],

            // Bebidas Frías
            ['codigo' => 'ICL', 'codigo_barra' => '7750001002015', 'nombre' => 'Iced Latte', 'categoria_id' => 2, 'unidad_medida' => 'NIU', 'precio_compra' => 4.50, 'precio_venta' => 13.00, 'stock' => 50, 'stock_minimo' => 15],
            ['codigo' => 'IAM', 'codigo_barra' => '7750001002022', 'nombre' => 'Iced Americano', 'categoria_id' => 2, 'unidad_medida' => 'NIU', 'precio_compra' => 3.40, 'precio_venta' => 10.00, 'stock' => 55, 'stock_minimo' => 15],
            ['codigo' => 'CHO', 'codigo_barra' => '7750001002039', 'nombre' => 'Chocolate Helado', 'categoria_id' => 2, 'unidad_medida' => 'NIU', 'precio_compra' => 4.20, 'precio_venta' => 12.00, 'stock' => 40, 'stock_minimo' => 12],
            ['codigo' => 'LIM', 'codigo_barra' => '7750001002046', 'nombre' => 'Limonada Andina', 'categoria_id' => 2, 'unidad_medida' => 'NIU', 'precio_compra' => 2.80, 'precio_venta' => 8.50, 'stock' => 45, 'stock_minimo' => 12],
            ['codigo' => 'AGU', 'codigo_barra' => '7750001002053', 'nombre' => 'Agua Mineral 500ml', 'categoria_id' => 2, 'unidad_medida' => 'NIU', 'precio_compra' => 1.20, 'precio_venta' => 4.00, 'stock' => 120, 'stock_minimo' => 30],
            ['codigo' => 'GAS', 'codigo_barra' => '7750001002060', 'nombre' => 'Gaseosa Inca Kola 500ml', 'categoria_id' => 2, 'unidad_medida' => 'NIU', 'precio_compra' => 2.40, 'precio_venta' => 6.00, 'stock' => 80, 'stock_minimo' => 20],

            // Frappé y Smoothies
            ['codigo' => 'FCR', 'codigo_barra' => '7750001003012', 'nombre' => 'Frappé de Caramelo', 'categoria_id' => 3, 'unidad_medida' => 'NIU', 'precio_compra' => 5.50, 'precio_venta' => 15.00, 'stock' => 35, 'stock_minimo' => 10],
            ['codigo' => 'FFR', 'codigo_barra' => '7750001003029', 'nombre' => 'Frappé de Fresa', 'categoria_id' => 3, 'unidad_medida' => 'NIU', 'precio_compra' => 5.50, 'precio_venta' => 15.00, 'stock' => 32, 'stock_minimo' => 10],
            ['codigo' => 'FMO', 'codigo_barra' => '7750001003036', 'nombre' => 'Frappé de Mora', 'categoria_id' => 3, 'unidad_medida' => 'NIU', 'precio_compra' => 5.80, 'precio_venta' => 16.00, 'stock' => 28, 'stock_minimo' => 10],
            ['codigo' => 'SCH', 'codigo_barra' => '7750001003043', 'nombre' => 'Frappé de Chocolate', 'categoria_id' => 3, 'unidad_medida' => 'NIU', 'precio_compra' => 5.80, 'precio_venta' => 16.00, 'stock' => 30, 'stock_minimo' => 10],
            ['codigo' => 'SMA', 'codigo_barra' => '7750001003050', 'nombre' => 'Smoothie de Papaya', 'categoria_id' => 3, 'unidad_medida' => 'NIU', 'precio_compra' => 5.00, 'precio_venta' => 14.00, 'stock' => 25, 'stock_minimo' => 8],
            ['codigo' => 'SFR', 'codigo_barra' => '7750001003067', 'nombre' => 'Smoothie de Fresa', 'categoria_id' => 3, 'unidad_medida' => 'NIU', 'precio_compra' => 5.20, 'precio_venta' => 14.50, 'stock' => 26, 'stock_minimo' => 8],

            // Tés
            ['codigo' => 'TCH', 'codigo_barra' => '7750001004019', 'nombre' => 'Té Chai Latte', 'categoria_id' => 4, 'unidad_medida' => 'NIU', 'precio_compra' => 4.20, 'precio_venta' => 12.00, 'stock' => 40, 'stock_minimo' => 12],
            ['codigo' => 'TMA', 'codigo_barra' => '7750001004026', 'nombre' => 'Té de Manzanilla', 'categoria_id' => 4, 'unidad_medida' => 'NIU', 'precio_compra' => 2.00, 'precio_venta' => 6.50, 'stock' => 50, 'stock_minimo' => 15],
            ['codigo' => 'TAN', 'codigo_barra' => '7750001004033', 'nombre' => 'Té Andino (Muña/Coca)', 'categoria_id' => 4, 'unidad_medida' => 'NIU', 'precio_compra' => 2.40, 'precio_venta' => 7.50, 'stock' => 45, 'stock_minimo' => 12],
            ['codigo' => 'TMI', 'codigo_barra' => '7750001004040', 'nombre' => 'Té de Menta', 'categoria_id' => 4, 'unidad_medida' => 'NIU', 'precio_compra' => 2.20, 'precio_venta' => 7.00, 'stock' => 38, 'stock_minimo' => 10],
            ['codigo' => 'TMA2', 'codigo_barra' => '7750001004057', 'nombre' => 'Matcha Latte', 'categoria_id' => 4, 'unidad_medida' => 'NIU', 'precio_compra' => 6.50, 'precio_venta' => 17.00, 'stock' => 22, 'stock_minimo' => 8],

            // Panadería y Pastelería
            ['codigo' => 'CRO', 'codigo_barra' => '7750001005016', 'nombre' => 'Croissant de Mantequilla', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 1.80, 'precio_venta' => 5.00, 'stock' => 40, 'stock_minimo' => 10],
            ['codigo' => 'PCQ', 'codigo_barra' => '7750001005023', 'nombre' => 'Pan con Queso', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 1.40, 'precio_venta' => 4.00, 'stock' => 35, 'stock_minimo' => 10],
            ['codigo' => 'EMD', 'codigo_barra' => '7750001005030', 'nombre' => 'Empanada de Pollo', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 2.20, 'precio_venta' => 6.00, 'stock' => 30, 'stock_minimo' => 10],
            ['codigo' => 'EMP', 'codigo_barra' => '7750001005047', 'nombre' => 'Empanada de Carne', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 2.20, 'precio_venta' => 6.00, 'stock' => 28, 'stock_minimo' => 10],
            ['codigo' => 'TOR', 'codigo_barra' => '7750001005054', 'nombre' => 'Torta de Chocolate (porción)', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 4.20, 'precio_venta' => 12.00, 'stock' => 20, 'stock_minimo' => 6],
            ['codigo' => 'BIS', 'codigo_barra' => '7750001005061', 'nombre' => 'Bizcocho de Naranja', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 1.80, 'precio_venta' => 5.00, 'stock' => 25, 'stock_minimo' => 8],
            ['codigo' => 'QUE', 'codigo_barra' => '7750001005078', 'nombre' => 'Queque de Miel (porción)', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 3.00, 'precio_venta' => 8.00, 'stock' => 18, 'stock_minimo' => 6],
            ['codigo' => 'ALFA', 'codigo_barra' => '7750001005085', 'nombre' => 'Alfajores (3 unidades)', 'categoria_id' => 5, 'unidad_medida' => 'NIU', 'precio_compra' => 2.20, 'precio_venta' => 6.50, 'stock' => 32, 'stock_minimo' => 10],

            // Desayunos y Almuerzos
            ['codigo' => 'DES', 'codigo_barra' => '7750001006013', 'nombre' => 'Desayuno Continental', 'categoria_id' => 6, 'unidad_medida' => 'NIU', 'precio_compra' => 8.00, 'precio_venta' => 22.00, 'stock' => 25, 'stock_minimo' => 8],
            ['codigo' => 'DEB', 'codigo_barra' => '7750001006020', 'nombre' => 'Desayuno Andino', 'categoria_id' => 6, 'unidad_medida' => 'NIU', 'precio_compra' => 9.50, 'precio_venta' => 25.00, 'stock' => 22, 'stock_minimo' => 8],
            ['codigo' => 'SAN', 'codigo_barra' => '7750001006037', 'nombre' => 'Sándwich de Pollo', 'categoria_id' => 6, 'unidad_medida' => 'NIU', 'precio_compra' => 5.50, 'precio_venta' => 15.00, 'stock' => 30, 'stock_minimo' => 10],
            ['codigo' => 'SAJ', 'codigo_barra' => '7750001006044', 'nombre' => 'Sándwich de Jamón y Queso', 'categoria_id' => 6, 'unidad_medida' => 'NIU', 'precio_compra' => 5.00, 'precio_venta' => 14.00, 'stock' => 32, 'stock_minimo' => 10],
            ['codigo' => 'ENS', 'codigo_barra' => '7750001006051', 'nombre' => 'Ensalada César con Pollo', 'categoria_id' => 6, 'unidad_medida' => 'NIU', 'precio_compra' => 8.50, 'precio_venta' => 22.00, 'stock' => 18, 'stock_minimo' => 6],
            ['codigo' => 'QSD', 'codigo_barra' => '7750001006068', 'nombre' => 'Quinoa Salad', 'categoria_id' => 6, 'unidad_medida' => 'NIU', 'precio_compra' => 7.00, 'precio_venta' => 19.00, 'stock' => 16, 'stock_minimo' => 5],

            // Postres
            ['codigo' => 'BRW', 'codigo_barra' => '7750001007010', 'nombre' => 'Brownie con Helado', 'categoria_id' => 7, 'unidad_medida' => 'NIU', 'precio_compra' => 4.20, 'precio_venta' => 12.00, 'stock' => 22, 'stock_minimo' => 6],
            ['codigo' => 'CHC', 'codigo_barra' => '7750001007027', 'nombre' => 'Cheesecake de Maracuyá', 'categoria_id' => 7, 'unidad_medida' => 'NIU', 'precio_compra' => 5.50, 'precio_venta' => 14.00, 'stock' => 20, 'stock_minimo' => 6],
            ['codigo' => 'TIR', 'codigo_barra' => '7750001007034', 'nombre' => 'Tiramisú', 'categoria_id' => 7, 'unidad_medida' => 'NIU', 'precio_compra' => 5.80, 'precio_venta' => 15.00, 'stock' => 18, 'stock_minimo' => 6],
            ['codigo' => 'HEL', 'codigo_barra' => '7750001007041', 'nombre' => 'Helado de 2 sabores', 'categoria_id' => 7, 'unidad_medida' => 'NIU', 'precio_compra' => 3.40, 'precio_venta' => 9.00, 'stock' => 25, 'stock_minimo' => 8],
            ['codigo' => 'MAZ', 'codigo_barra' => '7750001007058', 'nombre' => 'Mazamorra Morada', 'categoria_id' => 7, 'unidad_medida' => 'NIU', 'precio_compra' => 2.50, 'precio_venta' => 7.00, 'stock' => 22, 'stock_minimo' => 6],

            // Snacks
            ['codigo' => 'LAY', 'codigo_barra' => '7750001008017', 'nombre' => 'Papas Lay\'s 150g', 'categoria_id' => 8, 'unidad_medida' => 'NIU', 'precio_compra' => 3.20, 'precio_venta' => 8.50, 'stock' => 30, 'stock_minimo' => 10],
            ['codigo' => 'CHO2', 'codigo_barra' => '7750001008024', 'nombre' => 'Chocolate Sublime Clásico', 'categoria_id' => 8, 'unidad_medida' => 'NIU', 'precio_compra' => 1.20, 'precio_venta' => 3.00, 'stock' => 60, 'stock_minimo' => 15],
            ['codigo' => 'ORE', 'codigo_barra' => '7750001008031', 'nombre' => 'Galletas Oreo', 'categoria_id' => 8, 'unidad_medida' => 'NIU', 'precio_compra' => 2.50, 'precio_venta' => 6.50, 'stock' => 35, 'stock_minimo' => 10],
            ['codigo' => 'MAN', 'codigo_barra' => '7750001008048', 'nombre' => 'Maní Salado Andino 100g', 'categoria_id' => 8, 'unidad_medida' => 'NIU', 'precio_compra' => 1.80, 'precio_venta' => 5.00, 'stock' => 40, 'stock_minimo' => 10],
            ['codigo' => 'CHI', 'codigo_barra' => '7750001008055', 'nombre' => 'Chifles Crocantes 80g', 'categoria_id' => 8, 'unidad_medida' => 'NIU', 'precio_compra' => 1.40, 'precio_venta' => 4.00, 'stock' => 38, 'stock_minimo' => 10],

            // Granos y Merchandising
            ['codigo' => 'GRA', 'codigo_barra' => '7750001009014', 'nombre' => 'Café en Grano 250g (Andino)', 'categoria_id' => 9, 'unidad_medida' => 'NIU', 'precio_compra' => 18.00, 'precio_venta' => 38.00, 'stock' => 20, 'stock_minimo' => 5],
            ['codigo' => 'MOL', 'codigo_barra' => '7750001009021', 'nombre' => 'Café Molido 500g', 'categoria_id' => 9, 'unidad_medida' => 'NIU', 'precio_compra' => 28.00, 'precio_venta' => 58.00, 'stock' => 18, 'stock_minimo' => 5],
            ['codigo' => 'TER', 'codigo_barra' => '7750001009038', 'nombre' => 'Termo Café Andino 500ml', 'categoria_id' => 9, 'unidad_medida' => 'NIU', 'precio_compra' => 22.00, 'precio_venta' => 45.00, 'stock' => 15, 'stock_minimo' => 5],
            ['codigo' => 'TAS', 'codigo_barra' => '7750001009045', 'nombre' => 'Taza Cerámica Andina', 'categoria_id' => 9, 'unidad_medida' => 'NIU', 'precio_compra' => 9.00, 'precio_venta' => 22.00, 'stock' => 25, 'stock_minimo' => 8],
            ['codigo' => 'FIL', 'codigo_barra' => '7750001009052', 'nombre' => 'Filtros de Café x 100', 'categoria_id' => 9, 'unidad_medida' => 'NIU', 'precio_compra' => 6.00, 'precio_venta' => 15.00, 'stock' => 20, 'stock_minimo' => 6],
        ];

        foreach ($productos as $p) {
            $p['incluye_igv'] = true;
            $p['activo'] = true;
            $p['visible_pos'] = true;
            Producto::create($p);
        }
    }
}
