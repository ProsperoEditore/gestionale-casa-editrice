<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('contratti', function (Blueprint $table) {
            // Aumentiamo precisione da 5,2 a 8,2 (999999.99)
            $table->decimal('sconto_proprio_libro', 8, 2)->nullable()->change();
            $table->decimal('sconto_altri_libri', 8, 2)->nullable()->change();
            $table->decimal('royalties_vendite_indirette', 8, 2)->nullable()->change();
            $table->decimal('royalties_vendite_dirette', 8, 2)->nullable()->change();
            $table->decimal('royalties_eventi', 8, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('contratti', function (Blueprint $table) {
            // Torna alla versione precedente
            $table->decimal('sconto_proprio_libro', 5, 2)->nullable()->change();
            $table->decimal('sconto_altri_libri', 5, 2)->nullable()->change();
            $table->decimal('royalties_vendite_indirette', 5, 2)->nullable()->change();
            $table->decimal('royalties_vendite_dirette', 5, 2)->nullable()->change();
            $table->decimal('royalties_eventi', 5, 2)->nullable()->change();
        });
    }
};
