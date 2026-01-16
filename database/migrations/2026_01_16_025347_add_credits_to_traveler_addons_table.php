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
        Schema::table('traveler_addons', function (Blueprint $table) {
            // Change cost_per_person to allow negative values (for credits)
            // Add type column to differentiate add-ons from credits
            $table->enum('type', ['add_on', 'credit'])->default('add_on')->after('traveler_id');
        });
    }

    public function down(): void
    {
        Schema::table('traveler_addons', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
