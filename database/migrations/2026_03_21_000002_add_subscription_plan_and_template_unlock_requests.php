<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('plan_key')->nullable()->after('provider_id');
        });

        Schema::create('template_unlock_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('template_key');
            $table->decimal('amount', 10, 2)->default(499);
            $table->string('status')->default('pending')->index();
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_screenshot_path')->nullable();
            $table->text('comment')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_unlock_requests');

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('plan_key');
        });
    }
};
