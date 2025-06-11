<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('idStore')->nullable();       // ID retornado pela API do Mercado Pago
            $table->string('cpfcnpj')->nullable();
            $table->string('nameStore')->nullable();
            $table->string('endereco')->nullable();
            $table->string('estado')->nullable();
            $table->string('cep')->nullable();
            $table->string('cidade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
