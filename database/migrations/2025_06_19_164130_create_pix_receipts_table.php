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
        /* Schema::create('pix_receipts', function (Blueprint $table) {
            $table->id();

            $table->string('idTransacao');
            $table->string('tipoTransacao');
            $table->decimal('valor', 10, 2);
            $table->string('titulo');
            $table->string('txId');
            $table->string('nomePagador');
            $table->string('cpfCnpjPagador');
            $table->string('nomeEmpresaPagador');
            $table->string('numeroDocumento');
            $table->string('endToEndId');
            $table->string('status');
            
            $table->string('dataTransacao');$table->timestamps();
        }); */

        Schema::create('pix_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('external_reference')->nullable();
            $table->string('pos_id')->nullable();
            $table->string('status')->nullable();
            $table->string('store_id')->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->string('id_payment')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('id_store_internal')->nullable();
            $table->string('id_user_internal')->nullable();
            $table->timestamps();
        });

        /* Schema::create('pix_receipts', function (Blueprint $table) {
            $table->id();

            $table->decimal('valor', 10, 2);
            $table->string('nome_remetente');
            $table->string('cpf_remetente', 20);
            $table->string('id_mercado_pago')->unique();
            $table->string('id_pix')->unique();

            $table->string('pos_id')->nullable();
            $table->string('store_id')->nullable();
            $table->enum('status', ['Pendente', 'Processado', 'Falhou', 'Sucesso', 'Estornado', 'Processando'])->default('Pendente');

            $table->timestamps();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pix_receipts');
    }
}