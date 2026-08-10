<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('slug')->unique();
            $table->string('descripcion')->nullable();
            $table->string('color', 20)->default('#6c757d');
            $table->string('icono', 50)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
