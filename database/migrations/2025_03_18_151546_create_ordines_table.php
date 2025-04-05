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
        Schema::create('ordines', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->foreignId('anagrafica_id')->constrained('anagraficas');
            $table->enum('canale', ['vendite indirette', 'vendite dirette', 'eventi']);
            $table->enum('tipo_ordine', ['acquisto', 'conto deposito']);
            $table->timestamps();
        });
    
        Schema::create('ordine_titoli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordine_id')->constrained('ordines')->onDelete('cascade');
            $table->foreignId('libro_id')->constrained('libri');
            $table->integer('quantita');
            $table->decimal('prezzo_copertina', 8, 2);
            $table->decimal('valore_vendita_lordo', 8, 2);
            $table->decimal('sconto', 5, 2)->nullable();
            $table->decimal('iva', 5, 2)->nullable();
            $table->text('specifiche_iva')->nullable();
            $table->decimal('netto_a_pagare', 8, 2)->nullable();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordines');
    }
};
