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
            $table->string('causale')->nullable();
            $table->text('condizioni_conto_deposito')->nullable();
            $table->string('tempi_pagamento')->nullable();
            $table->text('modalita_pagamento')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('ordines', function (Blueprint $table) {
            $table->dropColumn('causale');
            $table->dropColumn('condizioni_conto_deposito');
            $table->dropColumn('tempi_pagamento');
            $table->dropColumn('modalita_pagamento');
        });
    }
    
};
