<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('short_videos', 'description')) {
            Schema::table('short_videos', function (Blueprint $table) {
                $table->text('description')->nullable()->after('title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('short_videos', 'description')) {
            Schema::table('short_videos', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
