<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('giacenze', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magazzino_id')->constrained('magazzini')->onDelete('cascade');
            $table->foreignId('libro_id')->constrained('libri')->onDelete('cascade');
            $table->string('isbn');
            $table->string('titolo');
            $table->integer('quantita');
            $table->decimal('prezzo', 8, 2);
            $table->decimal('sconto', 5, 2)->nullable();
            $table->decimal('costo_produzione', 8, 2)->nullable();
            $table->timestamp('data_ultimo_aggiornamento')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('giacenze');
    }
};
