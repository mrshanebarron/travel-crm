<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safari_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->integer('day_number');
            $table->date('date');
            $table->string('location');
            $table->string('lodge')->nullable();
            $table->string('morning_activity')->nullable();
            $table->string('midday_activity')->nullable();
            $table->string('afternoon_activity')->nullable();
            $table->string('other_activities')->nullable();
            $table->string('meal_plan')->nullable(); // FB, HB, BB, etc.
            $table->string('drink_plan')->nullable(); // Inclusive, Local, Premium
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safari_days');
    }
};
