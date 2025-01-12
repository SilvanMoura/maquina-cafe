<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('hora_transacao');
            $table->string('apelido');
            $table->string('nome_vendedor');
            $table->string('id_vendedor');
            $table->string('id_transacao')->unique();
            $table->decimal('valor_transacao', 10, 0);
            $table->string('status_transacao');
            $table->string('nome_comprador');
            $table->string('id_comprador');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
