<?php

/**
 * Default tasks auto-created when a new booking is created.
 * Based on the Master Checklist Google Sheet from Tapestry of Africa.
 *
 * Structure:
 * - name: Task description (matches Google Sheet exactly)
 * - days_before: Days before safari start date (null = immediate/on booking)
 * - days_after: Days after safari end date (for post-trip tasks)
 * - on_create: If true, due date is set to tomorrow (immediate action)
 * - assigned_to_name: Team member name for assignment lookup
 *
 * Team Members from Google Sheet:
 * - Matt: Handles payments and financial tasks
 * - Albert: Handles communications and client-facing tasks
 * - Hilda: Handles bookings, lodges, and post-trip follow-up
 * - Peter: Handles documentation, visas, insurance, and guides
 *
 * Timing Rules from Google Sheet:
 * - "Immediately upon booking" = on_create: true
 * - "X days before due" for payments = days_before relative to payment due date
 * - "X days before start" = days_before safari start
 * - "X days after booking" = calculated from booking creation
 * - "X days after end date" = days_after safari end
 */

return [
    'default_tasks' => [
        // === BOOKING CREATION TASKS (Immediately upon booking) ===
        [
            'name' => 'Deposit Received',
            'days_before' => null,
            'on_create' => true,
            'assigned_to_name' => 'Matt',
            'timing_description' => 'Immediately upon booking',
        ],
        [
            'name' => 'Receipt of Deposit email sent',
            'days_before' => null,
            'on_create' => true,
            'assigned_to_name' => 'Matt',
            'timing_description' => 'Immediately upon booking',
        ],
        [
            'name' => 'All lodges Booked',
            'days_before' => null,
            'on_create' => true,
            'assigned_to_name' => 'Hilda',
            'timing_description' => 'Immediately upon booking',
        ],
        [
            'name' => 'Safari Essential Email Series Activated',
            'days_before' => null,
            'on_create' => true,
            'assigned_to_name' => 'Albert',
            'timing_description' => 'Immediately upon booking',
        ],
        [
            'name' => 'Safari Guides assigned',
            'days_before' => null,
            'on_create' => true,
            'assigned_to_name' => 'Hilda',
            'timing_description' => 'Immediately upon booking',
        ],
        [
            'name' => 'Visas obtained for all travelers',
            'days_before' => null,
            'on_create' => true,
            'assigned_to_name' => 'Peter',
            'timing_description' => 'Immediately upon booking',
        ],

        // === PAYMENT MILESTONE TASKS ===
        [
            'name' => '90 Day payment received',
            'days_before' => 92, // 2 days before 90-day due date
            'assigned_to_name' => 'Matt',
            'timing_description' => '2 days before 90-day payment due',
        ],
        [
            'name' => '45 Day payment Received',
            'days_before' => 47, // 2 days before 45-day due date
            'assigned_to_name' => 'Matt',
            'timing_description' => '2 days before 45-day payment due',
        ],

        // === 30 DAYS AFTER BOOKING ===
        [
            'name' => 'Flight info received and entered',
            'days_after_booking' => 30, // 30 days after booking created
            'assigned_to_name' => 'Hilda',
            'timing_description' => '30 days after booking',
        ],

        // === 30 DAYS BEFORE SAFARI START ===
        [
            'name' => 'Insurance created and shared',
            'days_before' => 30,
            'assigned_to_name' => 'Peter',
            'timing_description' => '30 days before safari',
        ],
        [
            'name' => 'Arrival and Departure arrangements',
            'days_before' => 30,
            'assigned_to_name' => 'Albert',
            'timing_description' => '30 days before safari',
        ],

        // === 5 DAYS BEFORE SAFARI START ===
        [
            'name' => 'Safari guide invoice obtained',
            'days_before' => 5,
            'assigned_to_name' => 'Peter',
            'timing_description' => '5 days before safari',
        ],
        [
            'name' => 'Welcome communication sent',
            'days_before' => 5,
            'assigned_to_name' => 'Albert',
            'timing_description' => '1-2 weeks before arrival',
        ],

        // === 2 DAYS BEFORE SAFARI START ===
        [
            'name' => 'Client picked up and safari commenced',
            'days_before' => 2,
            'assigned_to_name' => 'Albert',
            'timing_description' => 'Safari start day',
        ],

        // === 1 DAY BEFORE SAFARI START ===
        [
            'name' => 'Client delivered to final destination',
            'days_before' => 1,
            'assigned_to_name' => 'Albert',
            'timing_description' => 'Safari end day',
        ],

        // === POST-SAFARI TASKS ===
        [
            'name' => 'Review request sent',
            'days_after' => 5,
            'assigned_to_name' => 'Hilda',
            'timing_description' => '5 days after safari ends',
        ],
    ],

    /**
     * Team member mapping.
     * Maps Google Sheet names to user lookup criteria.
     * In production, update these to match actual user names or emails.
     */
    'team_members' => [
        'Matt' => ['name' => 'Matt', 'role' => 'admin'],
        'Albert' => ['name' => 'Albert', 'role' => 'staff'],
        'Hilda' => ['name' => 'Hilda', 'role' => 'staff'],
        'Peter' => ['name' => 'Peter', 'role' => 'staff'],
    ],
];
