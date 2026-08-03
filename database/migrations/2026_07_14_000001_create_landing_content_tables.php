<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_how_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->unsignedTinyInteger('step_number');
            $table->string('title');
            $table->text('description');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_industries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('icon')->nullable();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->decimal('monthly_price', 8, 2);
            $table->decimal('yearly_price', 8, 2);
            $table->boolean('is_popular')->default(false);
            $table->json('features')->nullable();
            $table->string('button_text')->default('Subscribe Now');
            $table->string('badge_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('text');
            $table->string('author_name');
            $table->string('author_role')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('question');
            $table->text('answer');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('label');
            $table->string('value');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_stats');
        Schema::dropIfExists('landing_faqs');
        Schema::dropIfExists('landing_testimonials');
        Schema::dropIfExists('landing_plans');
        Schema::dropIfExists('landing_industries');
        Schema::dropIfExists('landing_how_steps');
        Schema::dropIfExists('landing_features');
    }
};
