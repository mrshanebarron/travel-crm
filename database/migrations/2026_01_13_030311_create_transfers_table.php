<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique(); // TR-2025-001 format
            $table->date('request_date');
            $table->enum('status', ['draft', 'sent', 'transfer_completed', 'vendor_payments_completed'])->default('draft');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('transfer_task_id')->nullable()->constrained('tasks')->onDelete('set null');
            $table->foreignId('vendor_task_id')->nullable()->constrained('tasks')->onDelete('set null');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('transfer_completed_at')->nullable();
            $table->timestamp('vendor_payments_completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
