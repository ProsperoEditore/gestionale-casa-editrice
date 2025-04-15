<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOldRoyaltiesColumnsFromContrattiTable extends Migration
{
    public function up()
    {
        Schema::table('contratti', function (Blueprint $table) {

            if (Schema::hasColumn('contratti', 'royalties_indirette_soglia_1')) {
                $table->dropColumn('royalties_indirette_soglia_1');
            }

            if (Schema::hasColumn('contratti', 'royalties_indirette_percentuale_1')) {
                $table->dropColumn('royalties_indirette_percentuale_1');
            }

            if (Schema::hasColumn('contratti', 'royalties_indirette_soglia_2')) {
                $table->dropColumn('royalties_indirette_soglia_2');
            }

            if (Schema::hasColumn('contratti', 'royalties_indirette_percentuale_2')) {
                $table->dropColumn('royalties_indirette_percentuale_2');
            }

            if (Schema::hasColumn('contratti', 'royalties_indirette_soglia_3')) {
                $table->dropColumn('royalties_indirette_soglia_3');
            }

            if (Schema::hasColumn('contratti', 'royalties_indirette_percentuale_3')) {
                $table->dropColumn('royalties_indirette_percentuale_3');
            }
        });
    }

    public function down()
    {
        // Non fare nulla nel metodo down(), quindi le colonne non verranno ripristinate in caso di rollback
    }
}
