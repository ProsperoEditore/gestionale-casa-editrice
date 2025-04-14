<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('access_anagrafiche')->default(false);
            $table->boolean('access_contratti')->default(false);
            $table->boolean('access_marchi')->default(false);
            $table->boolean('access_libri')->default(false);
            $table->boolean('access_magazzini')->default(false);
            $table->boolean('access_ordini')->default(false);
            $table->boolean('access_scarichi')->default(false);
            $table->boolean('access_registro_vendite')->default(false);
            $table->boolean('access_report')->default(false);
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'access_anagrafiche',
                'access_contratti',
                'access_marchi',
                'access_libri',
                'access_magazzini',
                'access_ordini',
                'access_scarichi',
                'access_registro_vendite',
                'access_report',
            ]);
        });
    }
};
