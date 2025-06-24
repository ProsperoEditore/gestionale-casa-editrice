<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profilo', function (Blueprint $table) {
            $table->id();

            $table->string('codice_destinatario')->nullable();
            $table->string('pec')->nullable();
            $table->string('nazione')->nullable();
            $table->string('partita_iva')->nullable();
            $table->string('codice_fiscale')->nullable();
            $table->string('denominazione')->nullable();
            $table->string('codice_eori')->nullable();
            $table->string('regime_fiscale')->nullable();
            $table->string('iban')->nullable();

            $table->string('indirizzo_amministrativa')->nullable();
            $table->string('numero_civico_amministrativa')->nullable();
            $table->string('cap_amministrativa')->nullable();
            $table->string('comune_amministrativa')->nullable();
            $table->string('provincia_amministrativa')->nullable();
            $table->string('nazione_amministrativa')->nullable();

            $table->string('indirizzo_operativa')->nullable();
            $table->string('numero_civico_operativa')->nullable();
            $table->string('cap_operativa')->nullable();
            $table->string('comune_operativa')->nullable();
            $table->string('provincia_operativa')->nullable();
            $table->string('nazione_operativa')->nullable();

            $table->boolean('sede_unica')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();

            $table->string('numero_rea')->nullable();
            $table->string('capitale_sociale')->nullable();
            $table->string('provincia_ufficio_rea')->nullable();
            $table->string('tipologia_soci')->nullable();
            $table->string('stato_liquidazione')->nullable();

            $table->string('rapp_nazione')->nullable();
            $table->string('rapp_partita_iva')->nullable();
            $table->string('rapp_codice_fiscale')->nullable();
            $table->string('rapp_denominazione')->nullable();
            $table->string('rapp_codice_eori')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profilo');
    }
};