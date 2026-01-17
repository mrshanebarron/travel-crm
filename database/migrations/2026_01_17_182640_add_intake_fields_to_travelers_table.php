<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travelers', function (Blueprint $table) {
            $table->string('passport_number', 50)->nullable()->after('phone');
            $table->date('passport_expiry')->nullable()->after('passport_number');
            $table->string('nationality', 100)->nullable()->after('passport_expiry');
            $table->text('dietary_requirements')->nullable()->after('nationality');
            $table->text('medical_conditions')->nullable()->after('dietary_requirements');
            $table->string('emergency_contact_name')->nullable()->after('medical_conditions');
            $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('travelers', function (Blueprint $table) {
            $table->dropColumn([
                'passport_number',
                'passport_expiry',
                'nationality',
                'dietary_requirements',
                'medical_conditions',
                'emergency_contact_name',
                'emergency_contact_phone',
            ]);
        });
    }
};
