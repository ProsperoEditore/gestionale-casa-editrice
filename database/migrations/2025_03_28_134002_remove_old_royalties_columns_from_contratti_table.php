<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOldRoyaltiesColumnsFromContrattiTable extends Migration
{
    public function up()
    {
        Schema::table('contratti', function (Blueprint $table) {
            // Rimuovi le colonne obsolete (correggi i nomi delle colonne)
            $table->dropColumn([
                'royalties_indirette_soglia_1',
                'royalties_indirette_percentuale_1',
                'royalties_indirette_soglia_2',
                'royalties_indirette_percentuale_2',
                'royalties_indirette_soglia_3',
                'royalties_indirette_percentuale_3',
            ]);
        });
    }

    public function down()
    {
        // Non fare nulla nel metodo down(), quindi le colonne non verranno ripristinate in caso di rollback
    }
}
