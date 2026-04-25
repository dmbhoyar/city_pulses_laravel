<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 30)->unique();

            // Client info
            $table->string('client_name', 150);
            $table->string('client_phone', 20)->nullable();
            $table->string('client_email', 150)->nullable();
            $table->text('client_address')->nullable();

            // Invoice meta
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');

            // Financials
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency', 5)->default('INR');

            // Notes
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();

            // Business info (overrides user's shop name if filled)
            $table->string('business_name', 150)->nullable();
            $table->string('business_phone', 20)->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_gstin', 25)->nullable();

            // Bank details
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_ifsc', 20)->nullable();
            $table->string('bank_holder', 100)->nullable();

            // Payment tracking
            $table->timestamp('payment_received_at')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_note', 255)->nullable();

            // Share tracking
            $table->timestamp('shared_at')->nullable();
            $table->string('shared_via', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
