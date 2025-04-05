<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contratti', function (Blueprint $table) {
            $table->id();
            $table->string('nome_contratto');
            $table->decimal('sconto_proprio_libro', 5, 2)->nullable();
            $table->decimal('sconto_altri_libri', 5, 2)->nullable();
            $table->decimal('royalties_vendite_indirette', 5, 2)->nullable();
            $table->decimal('royalties_vendite_dirette', 5, 2)->nullable();
            $table->decimal('royalties_eventi', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contratti');
    }
};
