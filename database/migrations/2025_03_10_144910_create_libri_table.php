<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('libri', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('titolo');
            $table->foreignId('marchio_editoriale_id')->constrained('marchio_editoriales')->onDelete('cascade');
            $table->string('collana')->nullable();
            $table->date('data_pubblicazione')->nullable();
            $table->integer('anno_pubblicazione');
            $table->decimal('prezzo', 8, 2);
            $table->decimal('costo_produzione', 8, 2)->nullable();
            $table->enum('stato', ['C', 'FC', 'A'])->default('C');
            $table->date('data_cessazione_commercio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('libri');
    }
};
