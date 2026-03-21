<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('service_reviews', 'reviewer_name')) {
                $table->string('reviewer_name')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('service_reviews', 'reviewer_email')) {
                $table->string('reviewer_email')->nullable()->after('reviewer_name');
            }
        });

        if (Schema::hasColumn('service_reviews', 'user_id')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE service_reviews MODIFY user_id BIGINT UNSIGNED NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE service_reviews ALTER COLUMN user_id DROP NOT NULL');
            }
        }
    }

    public function down(): void
    {
        Schema::table('service_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('service_reviews', 'reviewer_email')) {
                $table->dropColumn('reviewer_email');
            }

            if (Schema::hasColumn('service_reviews', 'reviewer_name')) {
                $table->dropColumn('reviewer_name');
            }
        });

        if (Schema::hasColumn('service_reviews', 'user_id')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE service_reviews MODIFY user_id BIGINT UNSIGNED NOT NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE service_reviews ALTER COLUMN user_id SET NOT NULL');
            }
        }
    }
};
