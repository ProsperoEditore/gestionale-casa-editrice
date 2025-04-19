<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rimuove il vecchio vincolo (se esiste)
        DB::statement("ALTER TABLE ordines DROP CONSTRAINT IF EXISTS ordines_canale_check");

        // Aggiunge il nuovo vincolo corretto
        DB::statement("
            ALTER TABLE ordines 
            ADD CONSTRAINT ordines_canale_check 
            CHECK (canale IN (
                'vendite dirette', 
                'vendite indirette', 
                'evento'
            ))
        ");
    }

    public function down(): void
    {
        // Rimuove il vincolo in caso di rollback
        DB::statement("ALTER TABLE ordines DROP CONSTRAINT IF EXISTS ordines_canale_check");
    }
};
