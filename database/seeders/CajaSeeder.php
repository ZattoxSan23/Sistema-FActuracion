<?php

namespace Database\Seeders;

use App\Models\Caja;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CajaSeeder extends Seeder
{
    public function run(): void
    {
        $cajeros = User::whereIn('rol', [User::ROL_CAJERA, User::ROL_ADMIN])->get();
        if ($cajeros->isEmpty()) {
            return;
        }

        $start = Carbon::now()->subYear()->startOfDay();
        $end = Carbon::now()->subDay()->endOfDay();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (random_int(1, 100) <= 5) {
                continue;
            }
            $cajero = $cajeros->random();
            $apertura = $date->copy()->setTime(7, random_int(0, 30));
            $montoApertura = random_int(80, 200);
            $cantidadVentas = DB::table('ventas')
                ->whereDate('fecha_emision', $date->format('Y-m-d'))
                ->count();
            if ($cantidadVentas === 0) {
                continue;
            }
            $cierre = $date->copy()->setTime(random_int(19, 21), random_int(0, 59));
            $caja = Caja::create([
                'user_id_apertura' => $cajero->id,
                'user_id_cierre' => $cajero->id,
                'fecha_apertura' => $apertura,
                'fecha_cierre' => $cierre,
                'monto_apertura' => $montoApertura,
                'monto_efectivo_teorico' => $montoApertura,
                'monto_efectivo_real' => $montoApertura,
                'diferencia' => 0,
                'total_ingresos' => 0,
                'total_egresos' => 0,
                'total_ventas_efectivo' => 0,
                'total_ventas_tarjeta' => 0,
                'total_ventas_yape' => 0,
                'total_ventas_transferencia' => 0,
                'cantidad_ventas' => 0,
                'estado' => 'cerrada',
                'observaciones_apertura' => 'Apertura del día',
                'observaciones_cierre' => 'Cierre normal',
                'created_at' => $apertura,
                'updated_at' => $cierre,
            ]);
            DB::table('ventas')
                ->whereDate('fecha_emision', $date->format('Y-m-d'))
                ->update(['caja_id' => $caja->id]);
            $caja->recalcularTotales();
        }
    }
}
