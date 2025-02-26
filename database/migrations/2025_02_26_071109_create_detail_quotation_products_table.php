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
        Schema::create('detail_quotation_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quotation_id');
            $table->bigInteger('product_id');
            $table->enum('unit', ['PC/PCS', 'Meter'])->default('PC/PCS');
            $table->bigInteger('qty');
            $table->bigInteger('price');
            $table->bigInteger('price_before_discount');
            $table->bigInteger('disc_percent');
            $table->bigInteger('disc_price_total');
            $table->bigInteger('discr_price_per_unit');
            $table->bigInteger('nett');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_quotation_products');
    }
};
