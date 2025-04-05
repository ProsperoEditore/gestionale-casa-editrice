<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('registro_tirature_dettagli', function (Blueprint $table) {
            $table->id();

            // Foreign key dichiarata manualmente
            $table->unsignedBigInteger('registro_tirature_id');
            $table->foreign('registro_tirature_id')->references('id')->on('registro_tirature')->onDelete('cascade');

            $table->foreignId('titolo_id')->constrained('libri')->onDelete('cascade');
            $table->date('data');
            $table->integer('copie_stampate');
            $table->decimal('prezzo_vendita_iva', 10, 3);
            $table->decimal('imponibile_relativo', 10, 3);
            $table->decimal('imponibile', 10, 3);
            $table->decimal('iva_4percento', 10, 3);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registro_tirature_dettagli');
    }
};
