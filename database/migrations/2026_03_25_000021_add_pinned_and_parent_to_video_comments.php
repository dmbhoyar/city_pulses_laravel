<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('video_comments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
                $table->foreign('parent_id')->references('id')->on('video_comments')->onDelete('cascade');
            }
            if (!Schema::hasColumn('video_comments', 'pinned')) {
                $table->boolean('pinned')->default(false)->after('comment_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('video_comments', function (Blueprint $table) {
            if (Schema::hasColumn('video_comments', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('video_comments', 'pinned')) {
                $table->dropColumn('pinned');
            }
        });
    }
};
