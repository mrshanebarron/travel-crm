<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('deposit_paid')->default(false)->after('deposit_locked');
            $table->boolean('payment_90_day_paid')->default(false)->after('payment_90_day');
            $table->boolean('payment_45_day_paid')->default(false)->after('payment_45_day');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['deposit_paid', 'payment_90_day_paid', 'payment_45_day_paid']);
        });
    }
};
