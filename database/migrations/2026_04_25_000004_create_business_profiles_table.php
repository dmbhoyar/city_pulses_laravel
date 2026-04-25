<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Tax / Legal
            $table->string('gstin', 25)->nullable();

            // Bank details
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_ifsc', 20)->nullable();
            $table->string('bank_holder', 100)->nullable();

            // Files — live-looked-up per print, not snapshotted on each invoice
            $table->string('payment_qr', 255)->nullable();
            $table->string('signature', 255)->nullable();

            // Default text — pre-fills invoice form
            $table->text('default_terms')->nullable();
            $table->text('default_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
