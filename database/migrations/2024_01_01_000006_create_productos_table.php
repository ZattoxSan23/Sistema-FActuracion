<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('codigo', 50)->unique();
            $table->string('codigo_barra', 50)->nullable()->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida', 10)->default('NIU'); // NIU, KG, LT, etc. (SUNAT)
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_mayorista', 10, 2)->nullable();
            $table->enum('tipo_afectacion_igv', ['10', '20', '30', '40'])->default('10'); // 10=Gravado, 20=Exonerado, 30=Inafecto, 40=Exportación
            $table->boolean('incluye_igv')->default(true);
            $table->boolean('activo')->default(true);
            $table->boolean('visible_pos')->default(true);
            $table->string('imagen')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('codigo');
            $table->index('codigo_barra');
            $table->index('nombre');
            $table->index('activo');
            $table->index('visible_pos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
