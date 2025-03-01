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
        Schema::create('quotation', function (Blueprint $table) {
            $table->id();
            $table->string('no')->unique();
            $table->text('address_letter')->nullable();
            $table->text('customer_id')->nullable();
            $table->text('phone')->nullable();
            $table->text('payent_type')->nullable();
            $table->enum('payment_type', ['CASH','BANK TRANSFER, PAY NOW'])->default('CASH');
            $table->enum('currency', ['IDR', 'USD', 'SGD'])->default('IDR');
            $table->date('date')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('address')->nullable();
            $table->text('contact_person')->nullable();
            $table->text('descriptions')->nullable();
            $table->text('remarks')->nullable();
            $table->text('account_options')->nullable();
            $table->text('made_by')->nullable();

            $table->text('sub_total')->nullable();
            $table->text('additional_discount')->nullable();
            $table->text('additional_cost')->nullable();
            $table->text('total')->nullable();
            $table->text('deposit')->nullable();
            $table->text('grand_total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation');
    }
};
