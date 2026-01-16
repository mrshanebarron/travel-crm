<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillBookingTasks extends Command
{
    protected $signature = 'bookings:backfill-tasks {--booking= : Specific booking ID to backfill}';
    protected $description = 'Add default checklist tasks to existing bookings that are missing them';

    public function handle()
    {
        $defaultTasks = config('booking_tasks.default_tasks', []);
        $teamMembers = config('booking_tasks.team_members', []);

        if (empty($defaultTasks)) {
            $this->error('No default tasks configured in config/booking_tasks.php');
            return 1;
        }

        // Get bookings to process
        $bookingId = $this->option('booking');
        if ($bookingId) {
            $bookings = Booking::where('id', $bookingId)->get();
        } else {
            $bookings = Booking::all();
        }

        $this->info("Processing {$bookings->count()} booking(s)...");

        $totalAdded = 0;

        foreach ($bookings as $booking) {
            $existingTaskNames = $booking->tasks()->pluck('name')->toArray();
            $tasksAdded = 0;

            $safariStartDate = $booking->start_date;
            $safariEndDate = $booking->end_date;
            $bookingCreatedDate = $booking->created_at;

            foreach ($defaultTasks as $taskData) {
                // Skip if task already exists
                if (in_array($taskData['name'], $existingTaskNames)) {
                    continue;
                }

                // Calculate due date based on task timing
                $dueDate = null;

                if (!empty($taskData['on_create'])) {
                    // Immediate tasks - set due to tomorrow from now
                    $dueDate = now()->addDay();
                } elseif (isset($taskData['days_before']) && $taskData['days_before'] !== null) {
                    // Days before safari start
                    $dueDate = $safariStartDate->copy()->subDays($taskData['days_before']);
                } elseif (isset($taskData['days_after'])) {
                    // Days after safari end
                    $dueDate = $safariEndDate->copy()->addDays($taskData['days_after']);
                } elseif (isset($taskData['days_after_booking'])) {
                    // Days after booking created
                    $dueDate = $bookingCreatedDate->copy()->addDays($taskData['days_after_booking']);
                }

                // Find assigned user
                $assignedTo = null;
                if (!empty($taskData['assigned_to_name'])) {
                    $memberConfig = $teamMembers[$taskData['assigned_to_name']] ?? null;
                    if ($memberConfig) {
                        $user = User::where('name', 'LIKE', '%' . $memberConfig['name'] . '%')->first();
                        $assignedTo = $user?->id;
                    }
                }

                // Create the task
                $booking->tasks()->create([
                    'name' => $taskData['name'],
                    'due_date' => $dueDate,
                    'assigned_to' => $assignedTo,
                    'assigned_by' => $assignedTo ? 1 : null, // System assignment
                    'assigned_at' => $assignedTo ? now() : null,
                    'timing_description' => $taskData['timing_description'] ?? null,
                    'status' => 'pending',
                ]);

                $tasksAdded++;
            }

            if ($tasksAdded > 0) {
                $this->line("  Booking #{$booking->booking_number}: Added {$tasksAdded} tasks");
                $totalAdded += $tasksAdded;
            } else {
                $this->line("  Booking #{$booking->booking_number}: Already has all tasks");
            }
        }

        $this->info("Done! Added {$totalAdded} tasks total.");

        return 0;
    }
}
