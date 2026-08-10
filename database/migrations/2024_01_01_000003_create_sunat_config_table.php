<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sunat_config', function (Blueprint $table) {
            $table->id();
            $table->enum('entorno', ['beta', 'produccion'])->default('beta');
            $table->enum('modo_envio', ['gre', 'ose'])->default('ose');
            $table->string('gre_url')->nullable();
            $table->string('ose_url')->nullable();
            $table->string('usuario_sol', 50)->nullable();
            $table->string('clave_sol', 100)->nullable();
            $table->string('certificado_path')->nullable();
            $table->string('certificado_password', 200)->nullable();
            $table->string('certificado_vence', 20)->nullable();
            $table->string('token_ose')->nullable();
            $table->timestamp('token_ose_vence')->nullable();
            $table->boolean('envio_automatico')->default(true);
            $table->integer('intentos_max')->default(3);
            $table->integer('timeout_segundos')->default(30);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sunat_config');
    }
};
