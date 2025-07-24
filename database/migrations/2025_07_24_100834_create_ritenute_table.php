<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ritenute', function (Blueprint $table) {
            $table->id();
            $table->string('numero'); // es. 01/2025
            $table->date('data_emissione');

            // Autore
            $table->string('nome_autore');
            $table->string('cognome_autore');
            $table->date('data_nascita');
            $table->string('luogo_nascita');
            $table->string('codice_fiscale');
            $table->string('iban')->nullable();
            $table->text('indirizzo')->nullable();

            // Marchio (per logo)
            $table->foreignId('marchio_id')->nullable()->constrained('marchio_editoriale');

            // Prestazioni e calcoli
            $table->json('prestazioni')->nullable(); // Descrizione + importo
            $table->decimal('totale', 8, 2)->default(0);
            $table->decimal('quota_esente', 8, 2)->default(0);
            $table->decimal('imponibile', 8, 2)->default(0);
            $table->decimal('ritenuta', 8, 2)->default(0);
            $table->decimal('netto_pagare', 8, 2)->default(0);

            $table->string('nota_iva')->nullable(); // esente iva
            $table->string('marca_bollo')->nullable()->default('€ 2,00 (per importi superiori a 77,47)');

            // Pagamenti
            $table->date('data_pagamento_netto')->nullable();
            $table->date('data_pagamento_ritenuta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ritenute');
    }
};
