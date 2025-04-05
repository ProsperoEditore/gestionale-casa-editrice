<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scaricos', function (Blueprint $table) {
            $table->id();

            // ✅ Collegamento facoltativo all'ordine associato
            $table->foreignId('ordine_id')->nullable()->constrained('ordines')->onDelete('set null');

            // ✅ Destinatario (collegato a Anagrafica)
            $table->foreignId('anagrafica_id')->nullable()->constrained('anagraficas')->onDelete('set null');

            // ✅ Info spedizione libera
            $table->text('info_spedizione')->nullable();

            // ✅ Campo testo libero "altro ordine"
            $table->string('altro_ordine')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scaricos');
    }
};
