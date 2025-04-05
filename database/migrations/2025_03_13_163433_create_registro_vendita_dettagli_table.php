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
        Schema::create('registro_vendita_dettagli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_vendita_id')->constrained('registro_vendites')->cascadeOnDelete();
            $table->date('data');
            $table->string('periodo');
            $table->string('isbn');
            $table->string('titolo');
            $table->integer('quantita');
            $table->decimal('prezzo', 8, 2);
            $table->decimal('valore_lordo', 10, 2);
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_vendita_dettagli');
    }
};
