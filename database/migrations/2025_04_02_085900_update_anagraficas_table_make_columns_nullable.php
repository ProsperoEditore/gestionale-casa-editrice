<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAnagraficasTableMakeColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anagraficas', function (Blueprint $table) {
            // Rendi i campi facoltativi
            $table->string('indirizzo_fatturazione')->nullable()->change();
            $table->string('indirizzo_spedizione')->nullable()->change();
            $table->string('partita_iva')->nullable()->change();
            $table->string('codice_fiscale')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('telefono')->nullable()->change();
            $table->string('pec')->nullable()->change();
            $table->string('codice_univoco')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('anagraficas', function (Blueprint $table) {
            // Rimuovi la facoltà di nullabilità (riporta tutto allo stato originale)
            $table->string('indirizzo_fatturazione')->nullable(false)->change();
            $table->string('indirizzo_spedizione')->nullable(false)->change();
            $table->string('partita_iva')->nullable(false)->change();
            $table->string('codice_fiscale')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
            $table->string('pec')->nullable(false)->change();
            $table->string('codice_univoco')->nullable(false)->change();
        });
    }
}
