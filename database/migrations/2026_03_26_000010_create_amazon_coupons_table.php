<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('amazon_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('store')->nullable();
            $table->string('image_url')->nullable();
            $table->string('discount_text')->nullable();
            $table->text('description')->nullable();
            $table->string('shop_url')->nullable();
            $table->integer('points_required')->nullable();
            $table->string('category')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amazon_coupons');
    }
};
