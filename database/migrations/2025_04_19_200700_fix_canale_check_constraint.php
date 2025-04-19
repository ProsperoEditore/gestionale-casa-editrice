<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ordines DROP CONSTRAINT IF EXISTS ordines_canale_check");

        DB::statement("
            ALTER TABLE ordines 
            ADD CONSTRAINT ordines_canale_check 
            CHECK (canale IN ('vendite dirette', 'vendite indirette', 'evento'))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ordines DROP CONSTRAINT IF EXISTS ordines_canale_check");
    }
};
