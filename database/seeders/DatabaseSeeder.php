<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Booking;
use App\Models\Group;
use App\Models\Traveler;
use App\Models\SafariDay;
use App\Models\Task;
use App\Models\Room;
use App\Models\Transfer;
use App\Models\TransferExpense;
use App\Models\LedgerEntry;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call RoleSeeder first to create roles and permissions
        $this->call(RoleSeeder::class);

        // Create team members per client spec:
        // Matt & Linda = super_admin, Hilda & Albert = admin, Peter = user

        // Super Admins
        $matt = User::create([
            'name' => 'Matt',
            'email' => 'matt@tapestryofafrica.com',
            'password' => Hash::make('password'),
            'phone' => '+16236062217',  // Matt's WhatsApp number
        ]);
        $matt->assignRole('super_admin');

        $linda = User::create([
            'name' => 'Linda',
            'email' => 'linda@tapestryofafrica.com',
            'password' => Hash::make('password'),
            'phone' => '+18016567966',  // Linda's WhatsApp number
        ]);
        $linda->assignRole('super_admin');

        // Admins
        $hilda = User::create([
            'name' => 'Hilda',
            'email' => 'hilda@tapestryofafrica.com',
            'password' => Hash::make('password'),
            'phone' => '+254712818547',  // Hilda's WhatsApp number
        ]);
        $hilda->assignRole('admin');

        $albert = User::create([
            'name' => 'Albert',
            'email' => 'albert@tapestryofafrica.com',
            'password' => Hash::make('password'),
            'phone' => '+254723682940',  // Albert's WhatsApp number
        ]);
        $albert->assignRole('admin');

        // User
        $peter = User::create([
            'name' => 'Peter',
            'email' => 'peter@tapestryofafrica.com',
            'password' => Hash::make('password'),
            'phone' => '+254714555485',  // Peter's WhatsApp number
        ]);
        $peter->assignRole('user');

        // Demo user for easy login (pre-filled on login page)
        $demo = User::create([
            'name' => 'Demo User',
            'email' => 'demo@travelcrm.com',
            'password' => Hash::make('Tr@vel2024!Demo'),
        ]);
        $demo->assignRole('super_admin');

        // Use Matt as the primary admin for demo data
        $admin = $matt;
        $staff1 = $hilda;
        $staff2 = $albert;

        // Booking 1: Upcoming Tanzania Safari
        $booking1 = Booking::create([
            'booking_number' => 'JA-2026-001',
            'country' => 'Tanzania',
            'start_date' => Carbon::now()->addDays(30),
            'end_date' => Carbon::now()->addDays(38),
            'status' => 'upcoming',
            'guides' => ['Michael Shayo', 'James Mwaniki'],
            'created_by' => $admin->id,
        ]);

        $group1 = Group::create(['booking_id' => $booking1->id, 'group_number' => 1]);

        Traveler::create([
            'group_id' => $group1->id,
            'first_name' => 'Robert',
            'last_name' => 'Johnson',
            'email' => 'robert.johnson@email.com',
            'phone' => '+1-555-123-4567',
            'dob' => '1975-03-15',
            'is_lead' => true,
            'order' => 0,
        ]);

        Traveler::create([
            'group_id' => $group1->id,
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.johnson@email.com',
            'phone' => '+1-555-123-4568',
            'dob' => '1978-07-22',
            'is_lead' => false,
            'order' => 1,
        ]);

        // Safari Days for Booking 1
        $locations = [
            ['location' => 'Arusha', 'lodge' => 'Arusha Coffee Lodge'],
            ['location' => 'Tarangire', 'lodge' => 'Tarangire Sopa Lodge'],
            ['location' => 'Tarangire', 'lodge' => 'Tarangire Sopa Lodge'],
            ['location' => 'Ngorongoro', 'lodge' => 'Ngorongoro Serena'],
            ['location' => 'Ngorongoro Crater', 'lodge' => 'Ngorongoro Serena'],
            ['location' => 'Serengeti', 'lodge' => 'Serengeti Serena'],
            ['location' => 'Serengeti', 'lodge' => 'Serengeti Serena'],
            ['location' => 'Serengeti', 'lodge' => 'Serengeti Serena'],
            ['location' => 'Arusha', 'lodge' => null],
        ];

        $startDate = Carbon::now()->addDays(30);
        foreach ($locations as $i => $loc) {
            SafariDay::create([
                'booking_id' => $booking1->id,
                'day_number' => $i + 1,
                'date' => $startDate->copy()->addDays($i),
                'location' => $loc['location'],
                'lodge' => $loc['lodge'],
                'morning_activity' => $i > 0 && $i < 8 ? 'Game Drive' : null,
                'afternoon_activity' => $i > 0 && $i < 8 ? 'Game Drive' : null,
                'meal_plan' => $loc['lodge'] ? 'FB' : 'BB',
                'drink_plan' => 'Local Inclusive',
            ]);
        }

        // Tasks for Booking 1
        Task::create([
            'booking_id' => $booking1->id,
            'name' => 'Confirm lodge reservations',
            'status' => 'completed',
            'assigned_to' => $staff1->id,
            'assigned_by' => $admin->id,
            'due_date' => Carbon::now()->subDays(5),
            'completed_at' => Carbon::now()->subDays(6),
        ]);

        Task::create([
            'booking_id' => $booking1->id,
            'name' => 'Collect passport copies',
            'status' => 'in_progress',
            'assigned_to' => $staff1->id,
            'assigned_by' => $admin->id,
            'due_date' => Carbon::now()->addDays(10),
        ]);

        Task::create([
            'booking_id' => $booking1->id,
            'name' => 'Send final itinerary',
            'status' => 'pending',
            'assigned_to' => $admin->id,
            'assigned_by' => $admin->id,
            'due_date' => Carbon::now()->addDays(20),
            'days_before_safari' => 10,
        ]);

        // Rooms for Booking 1
        Room::create([
            'booking_id' => $booking1->id,
            'type' => 'double',
            'adults' => 2,
        ]);

        // Ledger entries for Booking 1
        LedgerEntry::create([
            'booking_id' => $booking1->id,
            'date' => Carbon::now()->subDays(30),
            'description' => 'Deposit received',
            'type' => 'received',
            'amount' => 5000.00,
            'balance' => 5000.00,
            'created_by' => $admin->id,
        ]);

        LedgerEntry::create([
            'booking_id' => $booking1->id,
            'date' => Carbon::now()->subDays(10),
            'description' => '90-day payment received',
            'type' => 'received',
            'amount' => 5000.00,
            'balance' => 10000.00,
            'created_by' => $admin->id,
        ]);

        ActivityLog::create([
            'booking_id' => $booking1->id,
            'user_id' => $admin->id,
            'notes' => 'Booking created. Clients are celebrating 25th anniversary.',
        ]);

        // Booking 2: Active Kenya Safari
        $booking2 = Booking::create([
            'booking_number' => 'JA-2026-002',
            'country' => 'Kenya',
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(5),
            'status' => 'active',
            'guides' => ['Peter Kimani'],
            'created_by' => $admin->id,
        ]);

        $group2 = Group::create(['booking_id' => $booking2->id, 'group_number' => 1]);

        Traveler::create([
            'group_id' => $group2->id,
            'first_name' => 'Michael',
            'last_name' => 'Smith',
            'email' => 'mike.smith@email.com',
            'phone' => '+1-555-987-6543',
            'is_lead' => true,
            'order' => 0,
        ]);

        Traveler::create([
            'group_id' => $group2->id,
            'first_name' => 'Emily',
            'last_name' => 'Smith',
            'email' => 'emily.smith@email.com',
            'is_lead' => false,
            'order' => 1,
        ]);

        Traveler::create([
            'group_id' => $group2->id,
            'first_name' => 'Tom',
            'last_name' => 'Smith',
            'dob' => '2010-05-10',
            'is_lead' => false,
            'order' => 2,
        ]);

        // Safari Days for Booking 2
        $kenyaLocations = [
            ['location' => 'Nairobi', 'lodge' => 'Hemingways Nairobi'],
            ['location' => 'Amboseli', 'lodge' => 'Tortilis Camp'],
            ['location' => 'Amboseli', 'lodge' => 'Tortilis Camp'],
            ['location' => 'Masai Mara', 'lodge' => 'Governors Camp'],
            ['location' => 'Masai Mara', 'lodge' => 'Governors Camp'],
            ['location' => 'Masai Mara', 'lodge' => 'Governors Camp'],
            ['location' => 'Nairobi', 'lodge' => null],
        ];

        $startDate2 = Carbon::now()->subDays(2);
        foreach ($kenyaLocations as $i => $loc) {
            SafariDay::create([
                'booking_id' => $booking2->id,
                'day_number' => $i + 1,
                'date' => $startDate2->copy()->addDays($i),
                'location' => $loc['location'],
                'lodge' => $loc['lodge'],
                'morning_activity' => $i > 0 && $i < 6 ? 'Game Drive' : null,
                'afternoon_activity' => $i > 0 && $i < 6 ? 'Game Drive' : null,
                'meal_plan' => $loc['lodge'] ? 'FB' : 'BB',
            ]);
        }

        Room::create([
            'booking_id' => $booking2->id,
            'type' => 'family',
            'adults' => 2,
            'children_2_11' => 1,
        ]);

        // Booking 3: Completed Safari
        $booking3 = Booking::create([
            'booking_number' => 'JA-2025-015',
            'country' => 'Tanzania',
            'start_date' => Carbon::now()->subDays(45),
            'end_date' => Carbon::now()->subDays(38),
            'status' => 'completed',
            'guides' => ['David Msomi'],
            'created_by' => $admin->id,
        ]);

        $group3 = Group::create(['booking_id' => $booking3->id, 'group_number' => 1]);

        Traveler::create([
            'group_id' => $group3->id,
            'first_name' => 'William',
            'last_name' => 'Brown',
            'email' => 'wbrown@email.com',
            'is_lead' => true,
            'order' => 0,
        ]);

        // Transfer Request
        $transfer = Transfer::create([
            'transfer_number' => 'TR-2026-001',
            'request_date' => Carbon::now()->subDays(5),
            'status' => 'sent',
            'total_amount' => 12500.00,
            'created_by' => $admin->id,
            'sent_at' => Carbon::now()->subDays(4),
        ]);

        TransferExpense::create([
            'transfer_id' => $transfer->id,
            'booking_id' => $booking1->id,
            'category' => 'lodge',
            'vendor_name' => 'Serena Hotels',
            'amount' => 8500.00,
            'payment_type' => 'deposit',
            'notes' => 'Deposit for Serengeti and Ngorongoro properties',
        ]);

        TransferExpense::create([
            'transfer_id' => $transfer->id,
            'booking_id' => $booking1->id,
            'category' => 'guide_vehicle',
            'vendor_name' => 'Safari Vehicles Ltd',
            'amount' => 2500.00,
            'payment_type' => 'deposit',
        ]);

        TransferExpense::create([
            'transfer_id' => $transfer->id,
            'booking_id' => $booking1->id,
            'category' => 'park_entry',
            'vendor_name' => 'TANAPA',
            'amount' => 1500.00,
            'payment_type' => 'final',
            'notes' => 'Park fees for 2 pax',
        ]);

        // Second transfer in draft
        $transfer2 = Transfer::create([
            'transfer_number' => 'TR-2026-002',
            'request_date' => Carbon::now(),
            'status' => 'draft',
            'total_amount' => 0,
            'created_by' => $admin->id,
        ]);
    }
}
