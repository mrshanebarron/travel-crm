<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class AssignScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:assign-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign tasks based on their timing rules (runs daily)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for tasks that should be assigned based on timing rules...');

        // Find unassigned tasks that have an intended assignee and due date <= today
        $tasks = Task::whereNull('assigned_to')
            ->whereNotNull('intended_assignee')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', Carbon::today())
            ->with('booking')
            ->get();

        $assignedCount = 0;

        foreach ($tasks as $task) {
            // Look up user by intended assignee name
            $user = User::where('name', 'like', "%{$task->intended_assignee}%")->first();

            if ($user) {
                $task->update([
                    'assigned_to' => $user->id,
                    'assigned_at' => now(),
                    'assigned_by' => 1, // System assignment
                ]);

                $this->info("Assigned task '{$task->name}' to {$user->name} (Booking: {$task->booking->booking_number})");
                $assignedCount++;
            } else {
                $this->warn("Could not find user for intended assignee: {$task->intended_assignee} (Task: {$task->name})");
            }
        }

        $this->info("Assigned {$assignedCount} tasks based on timing rules.");

        return Command::SUCCESS;
    }
}
