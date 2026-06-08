<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('phone')->nullable();
            $table->string('local_address')->nullable();
            $table->string('full_address')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('company_second_logo')->nullable();
            $table->string('footer_logo')->nullable();
            $table->string('company_favicon')->nullable();
            $table->string('pan_vat')->nullable();
            $table->string('home_bg_img')->nullable();
            $table->text('brief_description')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('youtube')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('gmail')->nullable();


            $table->longText('map_url')->nullable();
            $table->longText('subscribe_sub_title')->nullable();
            $table->string('subscribe_main_title')->nullable();
            $table->longText('news_sub_title')->nullable();
            $table->string('news_main_title')->nullable();
            $table->longText('blog_sub_title')->nullable();
            $table->string('blog_main_title')->nullable();


            $table->string('first_main_title')->nullable();
            $table->string('second_main_title')->nullable();
            $table->string('third_main_title')->nullable();
            $table->string('fourth_main_title')->nullable();
            $table->string('fifth_main_title')->nullable();
            $table->string('sixth_main_title')->nullable();
            $table->string('seventh_main_title')->nullable();

            $table->longText('first_sub_title')->nullable();
            $table->longText('second_sub_title')->nullable();
            $table->longText('third_sub_title')->nullable();
            $table->longText('fourth_sub_title')->nullable();
            $table->longText('fifth_sub_title')->nullable();
            $table->longText('sixth_sub_title')->nullable();
            $table->longText('seventh_sub_title')->nullable();

            $table->string('footer_title_home')->nullable();
            $table->longText('footer_slogan')->nullable();
            $table->string('footer_title_other')->nullable();
            $table->longText('footer_slogan_other')->nullable();
            $table->string('image_one')->nullable();
            $table->string('image_two')->nullable();
            $table->string('image_three')->nullable();
            $table->string('image_four')->nullable();
            $table->string('image_five')->nullable();
            $table->string('image_six')->nullable();
            $table->string('image_seven')->nullable();



            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
