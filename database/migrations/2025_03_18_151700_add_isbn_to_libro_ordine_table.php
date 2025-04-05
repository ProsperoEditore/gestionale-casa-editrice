<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('libro_ordine', function (Blueprint $table) {
            $table->string('isbn')->after('libro_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('libro_ordine', function (Blueprint $table) {
            $table->dropColumn('isbn');
        });
    }
};
