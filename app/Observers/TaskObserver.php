<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\TwilioService;

class TaskObserver
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Send WhatsApp notification if task is assigned on creation
        if ($task->assigned_to && $task->assignedTo) {
            $this->twilioService->sendTaskAssignedNotification($task, $task->assignedTo);
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Check if task was just assigned (assigned_to changed from null to a user)
        if ($task->wasChanged('assigned_to') && $task->assigned_to && !$task->getOriginal('assigned_to')) {
            if ($task->assignedTo) {
                $this->twilioService->sendTaskAssignedNotification($task, $task->assignedTo);
            }
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
