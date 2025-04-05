<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoyaltiesSoglieToContrattiTable extends Migration
{
    public function up()
    {
        Schema::table('contratti', function (Blueprint $table) {
            // Aggiungi le colonne per soglie e percentuali
            $table->integer('royalties_vendite_indirette_soglia_1')->nullable()->default(200);
            $table->decimal('royalties_vendite_indirette_percentuale_1', 5, 2)->nullable()->default(5.00);
            $table->integer('royalties_vendite_indirette_soglia_2')->nullable()->default(499);
            $table->decimal('royalties_vendite_indirette_percentuale_2', 5, 2)->nullable()->default(7.50);
            $table->integer('royalties_vendite_indirette_soglia_3')->nullable()->default(500);
            $table->decimal('royalties_vendite_indirette_percentuale_3', 5, 2)->nullable()->default(10.00);
        });
    }

    public function down()
    {
        Schema::table('contratti', function (Blueprint $table) {
            // Rimuovi le colonne
            $table->dropColumn([
                'royalties_vendite_indirette_soglia_1',
                'royalties_vendite_indirette_percentuale_1',
                'royalties_vendite_indirette_soglia_2',
                'royalties_vendite_indirette_percentuale_2',
                'royalties_vendite_indirette_soglia_3',
                'royalties_vendite_indirette_percentuale_3'
            ]);
        });
    }
}
