<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            if (!Schema::hasColumn('updates', 'shop_id')) {
                $table->foreignId('shop_id')->nullable()->after('city_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            if (Schema::hasColumn('updates', 'shop_id')) {
                $table->dropConstrainedForeignId('shop_id');
            }
        });
    }
};
