<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibroOrdineTable extends Migration
{
    public function up()
    {
        Schema::create('libro_ordine', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordine_id')->constrained('ordines')->onDelete('cascade');
            $table->foreignId('libro_id')->constrained('libri')->onDelete('cascade');
            $table->integer('quantita');
            $table->decimal('prezzo', 10, 2);
            $table->decimal('valore_lordo', 10, 2);
            $table->decimal('sconto', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('libro_ordine');
    }
}
