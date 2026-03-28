<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('source_url')->nullable(); // Original YouTube/Instagram URL (kept private)
            $table->string('embed_id'); // extracted ID for embedding
            $table->enum('video_type', ['yt', 'ig']); // 'yt' for YouTube, 'ig' for Instagram
            $table->string('thumbnail_url')->nullable();
            $table->string('duration')->nullable(); // e.g., "0:58"
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('creator_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_videos');
    }
};
