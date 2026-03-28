<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruby_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tier_name', ['silver', 'gold', 'diamond', 'red']);
            $table->unsignedBigInteger('ruby_points');
            $table->timestamp('achieved_at');
            $table->timestamps();
            
            $table->unique(['user_id', 'tier_name']);
            $table->index('tier_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruby_tiers');
    }
};
