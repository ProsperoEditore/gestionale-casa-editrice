<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_vendites', function (Blueprint $table) {
            if (!Schema::hasColumn('registro_vendites', 'ordine_id')) {
                $table->unsignedBigInteger('ordine_id')->nullable()->after('anagrafica_id');
                $table->foreign('ordine_id')->references('id')->on('ordines')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registro_vendites', function (Blueprint $table) {
            if (Schema::hasColumn('registro_vendites', 'ordine_id')) {
                $table->dropForeign(['ordine_id']);
                $table->dropColumn('ordine_id');
            }
        });
    }
};
