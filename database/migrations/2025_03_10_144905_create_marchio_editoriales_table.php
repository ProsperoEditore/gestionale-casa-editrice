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
        Schema::create('marchio_editoriales', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('logo')->nullable();
            $table->string('sito_web')->nullable();
            $table->string('indirizzo_sede_legale')->nullable();
            $table->string('partita_iva')->nullable();
            $table->string('codice_univoco')->nullable();
            $table->string('iban')->nullable();
            $table->string('indirizzo_sede_logistica')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marchio_editoriales');
    }
};
