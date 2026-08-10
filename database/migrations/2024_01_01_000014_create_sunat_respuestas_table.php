<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sunat_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->nullable()->constrained('comprobantes')->nullOnDelete();
            $table->string('tipo_operacion', 30); // envio_cpe, consulta_cdr, comunicacion_baja, resumen_diario
            $table->string('endpoint', 200);
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->integer('http_status')->nullable();
            $table->string('codigo_respuesta', 10)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('exito')->default(false);
            $table->integer('duracion_ms')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->timestamps();

            $table->index('tipo_operacion');
            $table->index('exito');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sunat_respuestas');
    }
};
