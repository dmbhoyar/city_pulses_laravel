<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_page_visit_points')) {
            Schema::create('user_page_visit_points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('visit_date');
                $table->string('page_key', 191);
                $table->unsignedInteger('points_awarded')->default(1);
                $table->timestamps();

                $table->unique(['user_id', 'visit_date', 'page_key'], 'user_page_visit_unique');
                $table->index(['visit_date', 'page_key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_page_visit_points');
    }
};
