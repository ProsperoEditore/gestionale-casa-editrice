<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordine_titoli', function (Blueprint $table) {
            $table->string('info_spedizione')->nullable()->after('netto_a_pagare');
        });
    }

    public function down(): void
    {
        Schema::table('ordine_titoli', function (Blueprint $table) {
            $table->dropColumn('info_spedizione');
        });
    }
};
