<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->json('name');                       // transliterated in Bangla
            $table->json('city');
            $table->unsignedTinyInteger('rating');      // 1-5
            $table->string('traveler_type', 20)->nullable();   // Family, Couple, Friends, Solo
            $table->json('title');
            $table->json('body');
            $table->date('reviewed_on');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_approved', 'reviewed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
