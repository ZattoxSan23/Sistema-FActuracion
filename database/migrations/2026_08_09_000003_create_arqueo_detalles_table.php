<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arqueo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('denominacion', 10, 2);
            $table->integer('cantidad');
            $table->decimal('subtotal', 10, 2);
            $table->dateTime('fecha');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('caja_id');
            $table->index('denominacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arqueo_detalles');
    }
};
