<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE', 'PASAPORTE', 'SIN_DOCUMENTO'])->default('DNI');
            $table->string('numero_documento', 15);
            $table->string('nombre_razon_social', 200);
            $table->string('direccion')->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tipo_documento', 'numero_documento'], 'clientes_doc_unique');
            $table->index('nombre_razon_social');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
