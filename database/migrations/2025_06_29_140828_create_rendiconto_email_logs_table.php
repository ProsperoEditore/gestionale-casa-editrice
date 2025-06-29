<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendiconto_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magazzino_id')->constrained('magazzini')->onDelete('cascade');
            $table->string('email');
            $table->timestamps(); // include created_at e updated_at
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('rendiconto_email_logs');
    }
};
