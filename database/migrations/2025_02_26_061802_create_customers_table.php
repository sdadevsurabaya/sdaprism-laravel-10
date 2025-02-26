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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->unique();
            $table->text('address')->nullable();
            $table->text('city')->nullable();
            $table->text('province')->nullable();
            $table->text('postcode')->nullable();
            $table->text('country')->nullable();
            $table->text('telephone')->nullable();
            $table->text('fax')->nullable();
            $table->text('pic')->nullable();
            $table->text('mobile')->nullable();
            $table->text('email')->nullable();
            $table->text('terms')->nullable();
            $table->text('business_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
