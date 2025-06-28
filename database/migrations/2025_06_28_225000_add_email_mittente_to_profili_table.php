<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('profilo', function (Blueprint $table) {
        $table->string('email_mittente')->nullable()->after('pec');
    });
}

public function down()
{
    Schema::table('profilo', function (Blueprint $table) {
        $table->dropColumn('email_mittente');
    });
}

};
