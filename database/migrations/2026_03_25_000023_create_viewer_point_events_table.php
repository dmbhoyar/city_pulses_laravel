<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('viewer_point_events')) {
            Schema::create('viewer_point_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('event_type', 40);
                $table->string('subject_type', 40);
                $table->unsignedBigInteger('subject_id');
                $table->unsignedInteger('points')->default(0);
                $table->timestamps();

                $table->unique(['user_id', 'event_type', 'subject_type', 'subject_id'], 'viewer_points_unique_event');
                $table->index(['event_type', 'subject_type', 'subject_id'], 'viewer_points_subject_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('viewer_point_events');
    }
};
