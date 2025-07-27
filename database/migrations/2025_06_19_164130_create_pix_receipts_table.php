<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePixReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pix_receipts', function (Blueprint $table) {
            $table->id();

            $table->decimal('valor', 10, 2);
            $table->string('nome_remetente');
            $table->string('cpf_remetente', 20);
            $table->string('id_mercado_pago')->unique();
            $table->string('id_pix')->unique();

            $table->string('pos_id')->nullable();
            $table->string('store_id')->nullable();
            $table->enum('status', ['Pendente', 'Processado', 'Falhou', 'Sucesso', ' Estornado', 'Processando'])->default('Pendente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pix_receipts');
    }
}