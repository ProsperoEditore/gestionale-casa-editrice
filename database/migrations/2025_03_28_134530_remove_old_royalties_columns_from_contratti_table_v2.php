<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveOldRoyaltiesColumnsFromContrattiTableV2 extends Migration
{
    public function up()
    {
        $columnsToDrop = [
            'royalties_indirette_soglia_1',
            'royalties_indirette_percentuale_1',
            'royalties_indirette_soglia_2',
            'royalties_indirette_percentuale_2',
            'royalties_indirette_soglia_3',
            'royalties_indirette_percentuale_3',
        ];

        foreach ($columnsToDrop as $column) {
            if (Schema::hasColumn('contratti', $column)) {
                Schema::table('contratti', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down()
    {
        // Nessun rollback necessario
    }
}
