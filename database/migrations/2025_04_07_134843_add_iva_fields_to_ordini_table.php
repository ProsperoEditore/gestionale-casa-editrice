<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('ordines', function (Blueprint $table) {
        $table->string('specifiche_iva', 255)->nullable();
        $table->string('costo_spedizione', 255)->nullable();
        $table->string('altre_specifiche_iva', 255)->nullable();
    });
}

public function down()
{
    Schema::table('ordines', function (Blueprint $table) {
        $table->dropColumn(['specifiche_iva', 'costo_spedizione', 'altre_specifiche_iva']);
    });
}

};
