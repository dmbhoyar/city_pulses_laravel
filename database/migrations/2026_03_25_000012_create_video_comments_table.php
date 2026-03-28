<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_video_id')->constrained('short_videos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment_text');
            $table->timestamps();
            
            $table->index('short_video_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_comments');
    }
};
