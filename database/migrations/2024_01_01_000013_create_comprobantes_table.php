<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->string('tipo_comprobante', 2); // 01, 03, 07, 08
            $table->string('serie', 4);
            $table->integer('correlativo');
            $table->string('correlativo_completo', 20)->unique(); // B001-00000123
            $table->longText('xml_firmado')->nullable();
            $table->longText('xml_sin_firma')->nullable();
            $table->string('hash_cpe', 100)->nullable();
            $table->string('hash_cdr', 100)->nullable();
            $table->text('firma_path')->nullable(); // ruta del XML firmado
            $table->text('cdr_path')->nullable();
            $table->string('ticket')->nullable(); // ticket SUNAT
            $table->string('codigo_respuesta', 10)->nullable();
            $table->text('descripcion_respuesta')->nullable();
            $table->enum('estado', ['borrador', 'firmado', 'enviado', 'aceptado', 'rechazado', 'excepcion', 'anulado'])->default('borrador');
            $table->integer('intentos_envio')->default(0);
            $table->dateTime('fecha_firma')->nullable();
            $table->dateTime('fecha_envio')->nullable();
            $table->dateTime('fecha_respuesta')->nullable();
            $table->timestamps();

            $table->index('venta_id');
            $table->index('estado');
            $table->index('tipo_comprobante');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
