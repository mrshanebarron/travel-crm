<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['double', 'triple', 'single', 'family', 'other'])->default('double');
            $table->string('custom_type')->nullable(); // For 'other' type
            $table->integer('adults')->default(0);
            $table->integer('children_12_17')->default(0);
            $table->integer('children_2_11')->default(0);
            $table->integer('children_under_2')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
