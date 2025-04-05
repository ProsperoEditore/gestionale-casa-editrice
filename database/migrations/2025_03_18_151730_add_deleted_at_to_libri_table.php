<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('libri', function (Blueprint $table) {
            $table->softDeletes(); // Aggiunge la colonna deleted_at
        });
    }

    public function down()
    {
        Schema::table('libri', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Rimuove la colonna deleted_at se viene annullata la migrazione
        });
    }
    
};
