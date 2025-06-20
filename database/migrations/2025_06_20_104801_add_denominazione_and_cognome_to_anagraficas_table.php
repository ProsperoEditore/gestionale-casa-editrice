<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('anagraficas', function (Blueprint $table) {
        $table->string('denominazione')->nullable()->after('categoria');
        $table->string('cognome')->nullable()->after('nome');
    });
}

public function down(): void
{
    Schema::table('anagraficas', function (Blueprint $table) {
        $table->dropColumn(['denominazione', 'cognome']);
    });
}

};
