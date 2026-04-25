<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('business_name', 150)->nullable()->after('user_id');
            $table->string('business_phone', 20)->nullable()->after('business_name');
            $table->string('business_email', 150)->nullable()->after('business_phone');
            $table->text('business_address')->nullable()->after('business_email');
            $table->string('business_logo', 255)->nullable()->after('business_address');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'business_phone', 'business_email', 'business_address', 'business_logo']);
        });
    }
};
