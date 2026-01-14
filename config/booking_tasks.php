<?php

/**
 * Default tasks auto-created when a new booking is created.
 * These tasks are triggered based on timing relative to the safari start date.
 *
 * Structure:
 * - name: Task description
 * - days_before: Days before safari start date (null = immediate)
 * - days_after: Days after safari end date (for post-trip tasks)
 * - on_create: If true, due date is set to tomorrow (immediate action)
 *
 * Note: When the client provides their Google Sheet with the actual task list,
 * update this file or migrate to a database-driven solution.
 */

return [
    'default_tasks' => [
        // Pre-Booking Tasks
        ['name' => 'Send booking confirmation to client', 'days_before' => null, 'on_create' => true],
        ['name' => 'Collect passport copies from all travelers', 'days_before' => 120],
        ['name' => 'Collect traveler questionnaire', 'days_before' => 120],

        // 90-Day Tasks
        ['name' => 'Send 90-day payment reminder', 'days_before' => 95],
        ['name' => 'Confirm 25% payment received (90-day)', 'days_before' => 90],
        ['name' => 'Book internal flights', 'days_before' => 90],
        ['name' => 'Confirm lodge reservations', 'days_before' => 90],

        // 60-Day Tasks
        ['name' => 'Send detailed itinerary to client', 'days_before' => 60],
        ['name' => 'Confirm guide assignment', 'days_before' => 60],
        ['name' => 'Send packing list and travel tips', 'days_before' => 60],

        // 45-Day Tasks
        ['name' => 'Send 45-day payment reminder', 'days_before' => 50],
        ['name' => 'Confirm final payment received (50%)', 'days_before' => 45],
        ['name' => 'Process transfer to Tapestry of Africa', 'days_before' => 40],

        // 30-Day Tasks
        ['name' => 'Confirm all vendor payments completed', 'days_before' => 30],
        ['name' => 'Send pre-departure briefing document', 'days_before' => 30],
        ['name' => 'Verify all flight details are correct', 'days_before' => 30],

        // 14-Day Tasks
        ['name' => 'Final itinerary review', 'days_before' => 14],
        ['name' => 'Confirm airport pickup arrangements', 'days_before' => 14],
        ['name' => 'Send emergency contact information', 'days_before' => 14],

        // 7-Day Tasks
        ['name' => 'Send "Have a great trip" message', 'days_before' => 7],
        ['name' => 'Final check with guide and lodges', 'days_before' => 7],

        // Post-Safari Tasks
        ['name' => 'Send post-trip thank you and feedback request', 'days_after' => 3],
        ['name' => 'Follow up for testimonial/referrals', 'days_after' => 14],
    ],
];
