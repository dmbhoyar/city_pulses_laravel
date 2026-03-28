<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruby_tier_thresholds', function (Blueprint $table) {
            $table->id();
            $table->enum('tier_name', ['silver', 'gold', 'diamond', 'red'])->unique();
            $table->unsignedBigInteger('threshold_points');
            $table->foreignId('first_achiever_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('first_achieved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruby_tier_thresholds');
    }
};
