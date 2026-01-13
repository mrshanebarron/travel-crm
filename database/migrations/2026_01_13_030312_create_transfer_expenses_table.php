<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained();
            $table->enum('category', ['lodge', 'guide_vehicle', 'park_entry', 'misc'])->default('misc');
            $table->string('vendor_name')->nullable(); // Lodge/camp name or vendor
            $table->decimal('amount', 12, 2);
            $table->enum('payment_type', ['deposit', 'final', 'other'])->default('other');
            $table->text('notes')->nullable();
            $table->boolean('ledger_posted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_expenses');
    }
};
