<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update the category ENUM to include new expense categories
        DB::statement("ALTER TABLE transfer_expenses MODIFY COLUMN category ENUM('lodge', 'guide_vehicle', 'park_entry', 'misc', 'lodges_camps', 'driver_guide', 'arrival_dept_flight', 'internal_flights', 'driver_guide_invoices') DEFAULT 'misc'");
    }

    public function down(): void
    {
        // Revert to original ENUM values
        DB::statement("ALTER TABLE transfer_expenses MODIFY COLUMN category ENUM('lodge', 'guide_vehicle', 'park_entry', 'misc') DEFAULT 'misc'");
    }
};
