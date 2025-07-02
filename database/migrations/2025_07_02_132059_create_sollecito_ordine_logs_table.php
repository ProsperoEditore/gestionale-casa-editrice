<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('sollecito_ordine_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('ordine_id');
        $table->timestamps();

        $table->foreign('ordine_id')->references('id')->on('ordines')->onDelete('cascade');
    });
}
    public function down(): void
    {
        Schema::dropIfExists('sollecito_ordine_logs');
    }
};
