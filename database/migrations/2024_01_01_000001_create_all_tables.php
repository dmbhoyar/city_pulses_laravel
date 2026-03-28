<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('agmarknet_district')->nullable()->index();
            $table->string('agmarknet_market')->nullable()->index();
            $table->string('agmarknet_state')->nullable()->index();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('email')->unique();
            $table->string('mobile_number')->default('');
            $table->string('role')->default('user')->index();
            $table->text('experience')->nullable();
            $table->string('tags')->nullable();
            $table->foreignId('shop_id')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('reset_password_token')->nullable()->unique();
            $table->timestamp('reset_password_sent_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('template')->nullable();
            $table->json('page_config')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('shop_id')->references('id')->on('shops')->nullOnDelete();
        });

        Schema::create('farmings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->string('external_url')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->string('resume_url')->nullable();
            $table->foreignId('job_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('contact_number')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('district')->nullable()->index();
            $table->string('commodity')->nullable();
            $table->decimal('min_price', 12, 2)->nullable();
            $table->decimal('max_price', 12, 2)->nullable();
            $table->decimal('modal_price', 12, 2)->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->date('price_date')->nullable();
            $table->string('source_url')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('source')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->foreignId('shop_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('updates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('update_type')->default('general')->index();
            $table->string('source_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('updates');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('revenues');
        Schema::dropIfExists('markets');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('farmings');
        Schema::table('users', fn (Blueprint $table) => $table->dropForeign(['shop_id']));
        Schema::dropIfExists('shops');
        Schema::dropIfExists('users');
        Schema::dropIfExists('cities');
    }
};
