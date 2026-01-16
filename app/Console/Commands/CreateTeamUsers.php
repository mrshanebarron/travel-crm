<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateTeamUsers extends Command
{
    protected $signature = 'app:create-team-users';
    protected $description = 'Create team users for Tapestry of Africa (Matt, Linda, Hilda, Albert, Peter)';

    public function handle()
    {
        // Ensure roles exist
        $this->info('Creating roles and permissions...');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage_users', 'manage_system_settings',
            'view_financial_data', 'view_transfers',
            'modify_rates_payments', 'modify_ledger', 'manage_transfers',
            'view_bookings', 'modify_bookings', 'modify_activity_log',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $superAdmin->syncPermissions(Permission::all());
        $admin->syncPermissions(['view_bookings', 'modify_bookings', 'view_financial_data', 'view_transfers']);
        $user->syncPermissions(['view_bookings', 'modify_bookings']);

        // Create team users
        $teamUsers = [
            ['name' => 'Matt', 'email' => 'matt@tapestryofafrica.com', 'role' => 'super_admin'],
            ['name' => 'Linda', 'email' => 'linda@tapestryofafrica.com', 'role' => 'super_admin'],
            ['name' => 'Hilda', 'email' => 'hilda@tapestryofafrica.com', 'role' => 'admin'],
            ['name' => 'Albert', 'email' => 'albert@tapestryofafrica.com', 'role' => 'admin'],
            ['name' => 'Peter', 'email' => 'peter@tapestryofafrica.com', 'role' => 'user'],
        ];

        foreach ($teamUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'), // Default password - should be changed
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }

            $this->info("Created/updated user: {$userData['name']} ({$userData['email']}) - {$userData['role']}");
        }

        $this->info('');
        $this->info('Team users created successfully!');
        $this->info('Default password for all users: "password"');
        $this->warn('IMPORTANT: Users should change their passwords after first login.');

        return Command::SUCCESS;
    }
}
