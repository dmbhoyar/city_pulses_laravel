<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->string('delivery_name')->nullable()->after('processed_at');
            $table->string('delivery_phone', 20)->nullable()->after('delivery_name');
            $table->string('delivery_address1')->nullable()->after('delivery_phone');
            $table->string('delivery_address2')->nullable()->after('delivery_address1');
            $table->string('delivery_city', 100)->nullable()->after('delivery_address2');
            $table->string('delivery_state', 100)->nullable()->after('delivery_city');
            $table->string('delivery_pincode', 20)->nullable()->after('delivery_state');
            $table->string('delivery_landmark')->nullable()->after('delivery_pincode');
        });
    }

    public function down(): void
    {
        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_name','delivery_phone','delivery_address1','delivery_address2',
                'delivery_city','delivery_state','delivery_pincode','delivery_landmark',
            ]);
        });
    }
};
