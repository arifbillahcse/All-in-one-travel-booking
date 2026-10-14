<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Translatable columns (spatie/laravel-translatable) hold {"en": ..., "bn": ...}
 * so they are JSON. Nested content (itinerary, FAQ, ...) is JSON too: it is always
 * read and edited together with its destination, which keeps the admin forms simple.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('region');
            $table->json('tagline');
            $table->json('summary');                // short blurb for cards
            $table->json('overview_title');
            $table->unsignedInteger('price_from');
            $table->json('duration');
            $table->json('best_time');
            $table->json('distance');
            $table->json('style');
            $table->json('overview');
            $table->json('highlights');
            $table->json('attractions');
            $table->json('itinerary');
            $table->json('included');
            $table->json('excluded');
            $table->json('seasons');
            $table->json('transport');
            $table->json('tips');
            $table->json('faq');
            $table->json('gallery');
            $table->json('related')->nullable();   // slugs of related destinations
            $table->string('hero_image')->nullable();
            $table->string('card_image')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
