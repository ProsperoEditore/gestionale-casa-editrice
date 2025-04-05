<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('magazzini', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anagrafica_id')->constrained('anagraficas')->onDelete('cascade');
            $table->date('prossima_scadenza')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('magazzini');
    }
};
