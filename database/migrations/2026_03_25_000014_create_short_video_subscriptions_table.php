<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_video_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_video_id')->constrained('short_videos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->unique(['user_id', 'creator_id']);
            
            $table->index('short_video_id');
            $table->index('creator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_video_subscriptions');
    }
};
