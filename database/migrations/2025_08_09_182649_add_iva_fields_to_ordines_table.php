<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordines', function (Blueprint $table) {
            // aggiungi solo se non esistono già
            if (!Schema::hasColumn('ordines', 'aliquota_iva_ordine')) {
                $table->decimal('aliquota_iva_ordine', 5, 2)->default(0.00)->after('specifiche_iva');
            }
            if (!Schema::hasColumn('ordines', 'natura_iva_ordine')) {
                $table->string('natura_iva_ordine', 5)->nullable()->after('aliquota_iva_ordine');
            }
        });

        // Backfill per vecchi record (opzionale ma consigliato)
        // 1) se natura è valorizzata, forza aliquota = 0.00
        DB::table('ordines')
            ->whereNotNull('natura_iva_ordine')
            ->update(['aliquota_iva_ordine' => 0.00]);

        // 2) se natura è NULL e aliquota è NULL (edge cases), imposta aliquota 0.00
        DB::table('ordines')
            ->whereNull('natura_iva_ordine')
            ->whereNull('aliquota_iva_ordine')
            ->update(['aliquota_iva_ordine' => 0.00]);
    }

    public function down(): void
    {
        Schema::table('ordines', function (Blueprint $table) {
            if (Schema::hasColumn('ordines', 'natura_iva_ordine')) {
                $table->dropColumn('natura_iva_ordine');
            }
            if (Schema::hasColumn('ordines', 'aliquota_iva_ordine')) {
                $table->dropColumn('aliquota_iva_ordine');
            }
        });
    }
};
