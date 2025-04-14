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
            $table->string('tipo_ordine', 30)->change();
        });
    }
    
    public function down()
    {
        Schema::table('ordines', function (Blueprint $table) {
            $table->string('tipo_ordine', 10)->change(); // o il valore originale
        });
    }
    
};
