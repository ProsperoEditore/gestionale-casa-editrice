<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scheda_libros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained('libros')->onDelete('cascade');
            $table->text('descrizione_breve')->nullable();
            $table->text('sinossi')->nullable();
            $table->text('strillo')->nullable();
            $table->text('extra')->nullable();
            $table->text('biografia_autore')->nullable();
            $table->string('formato')->nullable();
            $table->integer('numero_pagine')->nullable();
            $table->string('copertina_path')->nullable();
            $table->string('copertina_stesa_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheda_libros');
    }
};
