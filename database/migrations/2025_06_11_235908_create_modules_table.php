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
        Schema::create('modules', function (Blueprint $table) {
            $table->id(); // campo id
            $table->string('modulo'); // campo codigo
            $table->string('store')->nullable();
            $table->string('user')->nullable();
            $table->string('status')->nullable();
            $table->string('rssi')->nullable();
            $table->string('status_online')->nullable();
            $table->string('ultima_conexao')->nullable();
            $table->string('sinal_qualidade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
