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
            $table->string('idStore');
            $table->string('status');
            $table->string('rssi');
            $table->string('status_online');
            $table->string('ultima_conexao');
            $table->string('sinal_qualidade');
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
