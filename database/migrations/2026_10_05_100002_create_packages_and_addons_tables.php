<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description');
            $table->json('best_for');
            $table->json('badge')->nullable();          // e.g. "Most Popular"
            $table->unsignedInteger('price');           // BDT per person, twin-sharing
            $table->unsignedTinyInteger('days');
            $table->unsignedTinyInteger('nights');
            $table->unsignedTinyInteger('destinations_count')->default(1);
            $table->json('features');                   // bullet list on the plan card
            $table->json('hotel');
            $table->json('meals');
            $table->json('transport');
            $table->json('cancellation');
            $table->boolean('has_guide')->default(false);
            $table->boolean('has_tickets')->default(false);
            $table->boolean('has_airport_transfer')->default(false);
            $table->boolean('has_trip_manager')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('icon');                     // key of an SVG icon in the Blade partial
            $table->json('title');
            $table->json('description');
            $table->unsignedInteger('price_from');
            $table->json('price_unit')->nullable();     // "/ day", "/ room"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
        Schema::dropIfExists('packages');
    }
};
