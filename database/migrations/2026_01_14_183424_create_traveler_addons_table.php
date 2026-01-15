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
        Schema::create('traveler_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traveler_id')->constrained()->onDelete('cascade');
            $table->string('experience_name');
            $table->decimal('cost_per_person', 12, 2);
            $table->text('notes')->nullable();
            $table->boolean('paid')->default(false);
            $table->foreignId('ledger_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traveler_addons');
    }
};
