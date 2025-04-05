<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ordines', function (Blueprint $table) {
            if (!Schema::hasColumn('ordines', 'codice')) {
                $table->string('codice')->nullable()->after('id')->unique();
            }
        });
    }

    public function down()
    {
        Schema::table('ordines', function (Blueprint $table) {
            $table->dropColumn('codice');
        });
    }
};
