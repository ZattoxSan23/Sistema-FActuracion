<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'yape', 'plin', 'transferencia', 'otros'])->default('efectivo');
            $table->decimal('monto', 10, 2);
            $table->decimal('vuelto', 10, 2)->default(0);
            $table->decimal('monto_recibido', 10, 2)->nullable(); // para efectivo
            $table->enum('tipo_tarjeta', ['debito', 'credito'])->nullable();
            $table->string('marca_tarjeta', 30)->nullable(); // Visa, Mastercard, etc.
            $table->string('numero_operacion', 50)->nullable();
            $table->string('numero_voucher', 50)->nullable();
            $table->string('cuenta_destino', 50)->nullable();
            $table->string('banco', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('venta_id');
            $table->index('metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
