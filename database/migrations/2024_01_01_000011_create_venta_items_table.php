<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->integer('orden')->default(0);
            $table->string('unidad_medida', 10)->default('NIU');
            $table->string('codigo_producto', 50)->nullable();
            $table->text('descripcion');
            $table->decimal('cantidad', 10, 3);
            $table->decimal('precio_unitario', 10, 4); // sin IGV
            $table->decimal('precio_unitario_con_igv', 10, 4)->nullable(); // con IGV
            $table->decimal('valor_unitario', 10, 4)->nullable(); // sin IGV para gratuitas
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2); // cantidad * precio
            $table->decimal('igv_item', 10, 2)->default(0);
            $table->decimal('total_item', 10, 2);
            $table->enum('tipo_afectacion_igv', ['10', '20', '30', '40'])->default('10');
            $table->boolean('gratuito')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('venta_id');
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_items');
    }
};
