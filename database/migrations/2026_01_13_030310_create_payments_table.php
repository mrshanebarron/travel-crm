<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traveler_id')->constrained()->onDelete('cascade');
            $table->decimal('safari_rate', 12, 2)->default(0);
            $table->decimal('deposit', 12, 2)->default(0); // 25%
            $table->decimal('payment_90_day', 12, 2)->default(0); // 25%
            $table->decimal('payment_45_day', 12, 2)->default(0); // 50%
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
