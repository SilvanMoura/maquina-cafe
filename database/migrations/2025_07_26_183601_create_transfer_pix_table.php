<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transfer_pix', function (Blueprint $table) {
            $table->id();
            $table->string('external_reference')->nullable();
            $table->string('pos_id')->nullable();
            $table->string('status')->nullable();
            $table->string('store_id')->nullable();
            $table->decimal('transaction_amount', 10, 2)->nullable();
            $table->string('id_payment')->nullable();
            $table->string('transaction_id');//->nullable();
            $table->string('receipt_id');//->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_pix');
    }
};

