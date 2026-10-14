<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('booking');     // booking | contact
            $table->string('status', 20)->default('new');       // new | contacted | confirmed | cancelled
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->string('topic')->nullable();                // contact form subject
            $table->date('travel_date')->nullable();
            $table->unsignedSmallInteger('guests')->nullable();
            $table->unsignedInteger('estimated_total')->nullable();
            $table->text('message')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
