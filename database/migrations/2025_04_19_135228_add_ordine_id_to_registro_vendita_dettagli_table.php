<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registro_vendita_dettagli', function (Blueprint $table) {
            $table->unsignedBigInteger('ordine_id')->nullable()->after('registro_vendita_id');

            $table->foreign('ordine_id')->references('id')->on('ordines')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('registro_vendita_dettagli', function (Blueprint $table) {
            $table->dropForeign(['ordine_id']);
            $table->dropColumn('ordine_id');
        });
    }
};
