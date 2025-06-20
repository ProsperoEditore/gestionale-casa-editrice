<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('anagraficas', function (Blueprint $table) {
            // nuovi campi per indirizzo fatturazione
            $table->string('via_fatturazione')->nullable();
            $table->string('civico_fatturazione')->nullable();
            $table->string('cap_fatturazione')->nullable();
            $table->string('comune_fatturazione')->nullable();
            $table->string('provincia_fatturazione')->nullable();
            $table->string('nazione_fatturazione')->default('IT');

            // nuovi campi per indirizzo spedizione
            $table->string('via_spedizione')->nullable();
            $table->string('civico_spedizione')->nullable();
            $table->string('cap_spedizione')->nullable();
            $table->string('comune_spedizione')->nullable();
            $table->string('provincia_spedizione')->nullable();
            $table->string('nazione_spedizione')->default('IT');
        });

                // LOGICA DI TRASFORMAZIONE DATI
        $anagrafiche = \App\Models\Anagrafica::all();

        foreach ($anagrafiche as $a) {
            // Fatturazione
            if (!empty($a->indirizzo_fatturazione)) {
                preg_match('/^(.*?),\s*(\d+)\s*-\s*(\d{5}),\s*(.*?)\s*\((\w{2})\)/', $a->indirizzo_fatturazione, $match);
                if ($match) {
                    $a->via_fatturazione = trim($match[1]);
                    $a->civico_fatturazione = trim($match[2]);
                    $a->cap_fatturazione = trim($match[3]);
                    $a->comune_fatturazione = trim($match[4]);
                    $a->provincia_fatturazione = trim($match[5]);
                    $a->nazione_fatturazione = 'IT';
                } else {
                    // fallback: salva tutto in via
                    $a->via_fatturazione = $a->indirizzo_fatturazione;
                }
            }

            // Spedizione
            if (!empty($a->indirizzo_spedizione)) {
                preg_match('/^(.*?),\s*(\d+)\s*-\s*(\d{5}),\s*(.*?)\s*\((\w{2})\)/', $a->indirizzo_spedizione, $match);
                if ($match) {
                    $a->via_spedizione = trim($match[1]);
                    $a->civico_spedizione = trim($match[2]);
                    $a->cap_spedizione = trim($match[3]);
                    $a->comune_spedizione = trim($match[4]);
                    $a->provincia_spedizione = trim($match[5]);
                    $a->nazione_spedizione = 'IT';
                } else {
                    $a->via_spedizione = $a->indirizzo_spedizione;
                }
            }

            $a->save();
        }
    }



    public function down()
    {
        Schema::table('anagraficas', function (Blueprint $table) {
            $table->dropColumn([
                'via_fatturazione', 'civico_fatturazione', 'cap_fatturazione', 'comune_fatturazione', 'provincia_fatturazione', 'nazione_fatturazione',
                'via_spedizione', 'civico_spedizione', 'cap_spedizione', 'comune_spedizione', 'provincia_spedizione', 'nazione_spedizione'
            ]);
        });
    }

};
