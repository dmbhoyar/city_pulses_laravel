<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            $table->integer('points_required')->default(200)->after('update_type');
        });
    }

    public function down(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            $table->dropColumn('points_required');
        });
    }
};
