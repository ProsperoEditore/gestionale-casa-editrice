<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('ruolo')->default('utente'); // valori: admin, utente, ecc.
        });
    }
    
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('ruolo');
        });
    }
    
};
