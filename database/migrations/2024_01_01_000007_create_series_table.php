<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_comprobante', ['01', '03', '07', '08', '09', '40'])->comment('01=Factura, 03=Boleta, 07=NC, 08=ND, 09=GuiaRemision, 40=Constancia');
            $table->string('serie', 4);
            $table->integer('correlativo_actual')->default(0);
            $table->integer('correlativo_desde')->default(1);
            $table->integer('correlativo_hasta')->default(99999999);
            $table->boolean('activo')->default(true);
            $table->boolean('principal')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['tipo_comprobante', 'serie'], 'series_tipo_serie_unique');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
