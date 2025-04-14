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
        Schema::table('scaricos', function (Blueprint $table) {
            $table->string('stato')->nullable()->default(null);
            $table->date('data_stato_info')->nullable()->default(null);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scaricos', function (Blueprint $table) {
            //
        });
    }
};
