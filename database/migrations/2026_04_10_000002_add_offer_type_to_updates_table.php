<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            // 'coupon' = reveal a code immediately after redemption
            // 'product' = physical delivery, tracked via admin order flow
            $table->enum('offer_category', ['coupon', 'product'])
                  ->default('coupon')
                  ->after('update_type');
        });
    }

    public function down(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            $table->dropColumn('offer_category');
        });
    }
};
