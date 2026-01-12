<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transfer_pix', function (Blueprint $table) {
            $table->id();

            $table->decimal('valor', 10, 2);
            $table->string('nome_remetente');
            $table->string('cpf_remetente', 14);

            $table->string('id_mercado_pago')->index();
            $table->string('id_pix')->unique();

            $table->string('pos_id')->index();
            $table->string('store_id')->index();

            $table->string('status')->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_pix');
    }
};
