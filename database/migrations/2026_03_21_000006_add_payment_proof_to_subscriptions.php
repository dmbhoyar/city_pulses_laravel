<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_transaction_id')->nullable()->after('provider_id');
            $table->string('payment_screenshot_path')->nullable()->after('payment_transaction_id');
            $table->text('comment')->nullable()->after('payment_screenshot_path');
            $table->text('admin_notes')->nullable()->after('comment');
            $table->foreignId('reviewed_by')->nullable()->after('admin_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'payment_transaction_id',
                'payment_screenshot_path',
                'comment',
                'admin_notes',
                'reviewed_at',
            ]);
        });
    }
};
