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
        Schema::create('registro_vendites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anagrafica_id')->constrained('anagraficas')->cascadeOnDelete();
            $table->enum('canale', ['Vendite indirette', 'Vendite dirette', 'Eventi', 'Altro']);
            $table->timestamps();
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_vendites');
    }
};
