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
        Schema::create('safari_day_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safari_day_id')->constrained()->cascadeOnDelete();
            $table->enum('period', ['morning', 'midday', 'afternoon', 'evening']);
            $table->string('activity');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['safari_day_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safari_day_activities');
    }
};
