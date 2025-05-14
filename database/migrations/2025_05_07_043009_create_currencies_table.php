<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10); // Contoh: USD, IDR, EUR
            $table->string('symbol', 10)->nullable(); // Contoh: $, Rp, €
            $table->string('name'); // Nama lengkap: US Dollar, Rupiah
            $table->boolean('is_active')->default(false); // Mata uang yang aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
