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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();          // Path atau URL logo
            $table->string('header_logo_id')->nullable();          // Path atau URL logo
            $table->string('title')->nullable();                // Judul
            $table->string('footer_text')->nullable();          // Footer text
            $table->date('date')->nullable();                   // Tanggal
            $table->string('currency_id')->nullable();         // Mata uang (misal: IDR, USD)
            $table->boolean('show_payment_method')->default(0);// Menampilkan metode pembayaran
            $table->string('payment_method')->nullable();       // Nama metode pembayaran (opsional)
            $table->text('notes')->nullable();           // Catatan (maks 100 karakter)
            $table->json('datatable_data')->nullable();         // Data JSON dari DataTables
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
