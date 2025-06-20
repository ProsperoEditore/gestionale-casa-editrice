<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    // Colonna già esistente: disattivata per evitare errore su Heroku
    // Schema::table('ordines', function (Blueprint $table) {
    //     $table->string('pagato', 250)->nullable()->after('tipo_ordine');
    // });
}

public function down(): void
{
    // Schema::table('ordines', function (Blueprint $table) {
    //     $table->dropColumn('pagato');
    // });
}

};
