<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('scarichi_richiesti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordine_id')->constrained()->onDelete('cascade');
            $table->foreignId('libro_id')->constrained()->onDelete('cascade');
            $table->foreignId('magazzino_id')->constrained()->onDelete('cascade');
            $table->integer('quantita');
            $table->enum('stato', ['in attesa', 'approvato', 'rifiutato'])->default('in attesa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scarichi_richiesti');
    }
};
