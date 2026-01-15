<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Role Permissions based on client spec:
     *
     * SUPERADMIN - Full access to everything
     * - View: All data
     * - Modify: Rates, payments, deposits, ledgers, transfers, activity logs, users, settings
     *
     * ADMIN - Non-financial booking management
     * - View: All non-financial booking data
     * - Modify: Itineraries, travelers, rooms, flights, documents, notes
     * - Cannot: Modify rates/payments, edit activity logs, manage transfers, manage users
     *
     * USER - View-only (non-financial)
     * - View: Client profiles, itineraries, travelers, rooms, flights, internal notes
     * - Cannot View: Rates, payments, deposits, ledgers, transfers, financial data
     * - Modify: Limited non-financial booking details only
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions - grouped by category
        $permissions = [
            // User & System Management
            'manage_users',
            'manage_system_settings',

            // Financial - Viewing
            'view_financial_data',      // Rates, payments, deposits, ledgers
            'view_transfers',           // Transfers & disbursements

            // Financial - Modifying
            'modify_rates_payments',    // Edit client rates, payments, deposits
            'modify_ledger',            // Edit ledger entries
            'manage_transfers',         // Request, approve, edit transfers

            // Booking - Viewing
            'view_bookings',            // Non-financial booking data

            // Booking - Modifying
            'modify_bookings',          // Itineraries, travelers, rooms, flights, docs, notes

            // Activity Log
            'modify_activity_log',      // Edit/delete activity log entries
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // SUPERADMIN - Everything
        $superAdmin->syncPermissions(Permission::all());

        // ADMIN - Non-financial booking management
        $admin->syncPermissions([
            'view_bookings',
            'modify_bookings',
            'view_financial_data',      // Can VIEW but not modify
            'view_transfers',           // Can VIEW but not modify
        ]);

        // USER - View-only (non-financial only)
        $user->syncPermissions([
            'view_bookings',
            'modify_bookings',          // Limited non-financial modifications
            // NO financial viewing or modifying permissions
        ]);
    }
}
