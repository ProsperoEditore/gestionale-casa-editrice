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
        Schema::create('anagraficas', function (Blueprint $table) {
            $table->id();
            $table->enum('categoria', ['magazzino editore', 'sito', 'libreria c.e.', 'libreria cliente', 'privato', 'biblioteca', 'associazione', 'università', 'grossista', 'distributore', 'fiere', 'festival', 'altro']);
            $table->string('nome');
            $table->string('indirizzo_fatturazione')->nullable();
            $table->string('indirizzo_spedizione');
            $table->string('partita_iva')->nullable();
            $table->string('codice_fiscale')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('pec')->nullable();
            $table->string('codice_univoco')->nullable();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anagraficas');
    }
};
