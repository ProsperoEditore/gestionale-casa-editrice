<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_dettagli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade'); // Collega ai report
            $table->date('data_vendita');
            $table->foreignId('anagrafica_id')->constrained('anagraficas')->onDelete('cascade'); // Cliente
            $table->integer('quantita');
            $table->decimal('valore_copertina', 8, 2);
            $table->decimal('valore_vendita_lordo', 8, 2);
            $table->decimal('percentuale_dovuta', 5, 2);
            $table->decimal('royalties', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_dettagli');
    }
};
