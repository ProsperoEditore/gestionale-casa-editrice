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
        DB::statement("ALTER TABLE ordines DROP CONSTRAINT IF EXISTS ordines_canale_check");
        DB::statement("ALTER TABLE ordines ADD CONSTRAINT ordines_canale_check CHECK (canale IN ('diretta', 'indiretta', 'evento', 'omaggio', 'acquisto autore'))");
    }
    
    public function down()
    {
        DB::statement("ALTER TABLE ordines DROP CONSTRAINT IF EXISTS ordines_canale_check");
        DB::statement("ALTER TABLE ordines ADD CONSTRAINT ordines_canale_check CHECK (canale IN ('diretta', 'indiretta', 'evento'))");
    }
    
};
