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
        Schema::table('registro_vendites', function (Blueprint $table) {
            $table->renameColumn('canale', 'canale_vendita');
        });
    }
    
    public function down()
    {
        Schema::table('registro_vendites', function (Blueprint $table) {
            $table->renameColumn('canale_vendita', 'canale');
        });
    }
    
};
