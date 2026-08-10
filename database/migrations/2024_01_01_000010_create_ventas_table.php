<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('correlativo', 20)->unique(); // Ej: B001-00000123
            $table->enum('tipo_comprobante', ['01', '03', '07', '08'])->default('03'); // 01=Factura, 03=Boleta, 07=NC, 08=ND
            $table->string('serie', 4);
            $table->integer('numero');
            $table->dateTime('fecha_emision');
            $table->string('moneda', 3)->default('PEN');
            $table->decimal('op_gravadas', 10, 2)->default(0);
            $table->decimal('op_exoneradas', 10, 2)->default(0);
            $table->decimal('op_inafectas', 10, 2)->default(0);
            $table->decimal('op_gratuitas', 10, 2)->default(0);
            $table->decimal('descuento_global', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['registrada', 'anulada', 'emitida', 'rechazada'])->default('registrada');
            $table->enum('estado_sunat', ['pendiente', 'enviado', 'aceptado', 'rechazado', 'excepcion', 'anulado'])->default('pendiente');
            $table->text('motivo_anulacion')->nullable();
            $table->foreignId('user_id_anulacion')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_anulacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tipo_comprobante');
            $table->index('serie');
            $table->index('numero');
            $table->index('fecha_emision');
            $table->index('estado');
            $table->index('estado_sunat');
        });

        Schema::table('caja_movimientos', function (Blueprint $table) {
            $table->foreign('venta_id')->references('id')->on('ventas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('caja_movimientos', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
        });

        Schema::dropIfExists('ventas');
    }
};
