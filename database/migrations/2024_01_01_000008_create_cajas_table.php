<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_apertura')->constrained('users');
            $table->foreignId('user_id_cierre')->nullable()->constrained('users');
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_apertura', 10, 2)->default(0);
            $table->decimal('monto_efectivo_teorico', 10, 2)->default(0);
            $table->decimal('monto_efectivo_real', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->default(0);
            $table->decimal('total_ingresos', 10, 2)->default(0);
            $table->decimal('total_egresos', 10, 2)->default(0);
            $table->decimal('total_ventas_efectivo', 10, 2)->default(0);
            $table->decimal('total_ventas_tarjeta', 10, 2)->default(0);
            $table->decimal('total_ventas_yape', 10, 2)->default(0);
            $table->decimal('total_ventas_transferencia', 10, 2)->default(0);
            $table->integer('cantidad_ventas')->default(0);
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('observaciones_apertura')->nullable();
            $table->text('observaciones_cierre')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha_apertura');
            $table->index('user_id_apertura');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
