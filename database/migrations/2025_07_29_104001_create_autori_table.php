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
        Schema::create('autori', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->string('cognome')->nullable();
            $table->string('pseudonimo')->nullable();
            $table->string('denominazione')->nullable();
            $table->string('codice_fiscale')->nullable();
            $table->date('data_nascita')->nullable();
            $table->string('luogo_nascita')->nullable();
            $table->string('iban')->nullable();
            $table->string('indirizzo')->nullable();
            $table->text('biografia')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autori');
    }
};
