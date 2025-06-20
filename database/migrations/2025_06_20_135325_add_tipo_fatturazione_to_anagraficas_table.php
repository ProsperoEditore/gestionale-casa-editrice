<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('anagraficas', function (Blueprint $table) {
        $table->string('tipo_fatturazione')->nullable()->after('pec'); // o 'codice_univoco'
    });
}

public function down()
{
    Schema::table('anagraficas', function (Blueprint $table) {
        $table->dropColumn('tipo_fatturazione');
    });
}

};
