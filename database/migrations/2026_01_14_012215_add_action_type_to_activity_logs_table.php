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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('action_type')->default('manual')->after('user_id');
            $table->string('entity_type')->nullable()->after('action_type');
            $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['action_type', 'entity_type', 'entity_id']);
        });
    }
};
