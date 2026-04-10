<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->unsignedBigInteger('offer_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('coupon_ref_id')->nullable()->after('offer_id');
            $table->enum('status', ['pending', 'approved', 'on_the_way', 'delivered', 'rejected'])
                  ->default('pending')->after('redeemed_at');
            $table->text('admin_notes')->nullable()->after('status');
            $table->timestamp('processed_at')->nullable()->after('admin_notes');

            $table->foreign('offer_id')->references('id')->on('updates')->onDelete('set null');
            $table->foreign('coupon_ref_id')->references('id')->on('coupons')->onDelete('set null');

            $table->index('status');
            $table->index('offer_id');
        });
    }

    public function down(): void
    {
        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->dropForeign(['offer_id']);
            $table->dropForeign(['coupon_ref_id']);
            $table->dropColumn(['offer_id', 'coupon_ref_id', 'status', 'admin_notes', 'processed_at']);
        });
    }
};
