<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11)->unique();
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200)->nullable();
            $table->string('direccion', 250);
            $table->string('ubigeo', 6)->nullable();
            $table->string('departamento', 50)->nullable();
            $table->string('provincia', 50)->nullable();
            $table->string('distrito', 50)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            $table->string('logo_path')->nullable();
            $table->decimal('igv', 5, 2)->default(18.00);
            $table->string('moneda', 3)->default('PEN');
            $table->string('tipo_precio', 20)->default('incluye_igv'); // incluye_igv | no_incluye_igv
            $table->text('pie_pagina_ticket')->nullable();
            $table->text('mensaje_personalizado')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
