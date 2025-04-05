<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scaricos', function (Blueprint $table) {
            $table->string('destinatario_nome')->nullable()->after('anagrafica_id');
        });
    }

    public function down(): void
    {
        Schema::table('scaricos', function (Blueprint $table) {
            $table->dropColumn('destinatario_nome');
        });
    }
};
