<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_vendites', function (Blueprint $table) {
            $table->integer('periodo')->nullable()->after('anagrafica_id');
        });
    }

    public function down(): void
    {
        Schema::table('registro_vendites', function (Blueprint $table) {
            $table->dropColumn('periodo');
        });
    }
};
