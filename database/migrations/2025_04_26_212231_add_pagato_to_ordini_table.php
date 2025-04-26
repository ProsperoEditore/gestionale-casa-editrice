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
        Schema::table('ordini', function (Blueprint $table) {
            $table->string('pagato', 250)->nullable()->after('tipo_ordine');
        });
    }
    
    public function down()
    {
        Schema::table('ordini', function (Blueprint $table) {
            $table->dropColumn('pagato');
        });
    }
    
};
