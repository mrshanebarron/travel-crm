<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Livewire handles all the data now
        return view('tasks.index');
    }

    public function store(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'days_before_safari' => 'nullable|integer|min:0',
            'timing_description' => 'nullable|string|max:255',
        ]);

        $task = $booking->tasks()->create([
            ...$validated,
            'assigned_by' => auth()->id(),
            'assigned_at' => $validated['assigned_to'] ? now() : null,
        ]);

        // Log task creation
        if (!empty($validated['assigned_to'])) {
            $assignee = User::find($validated['assigned_to']);
            $booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_created',
                'notes' => "Task created and assigned to {$assignee->name}: {$validated['name']}",
            ]);
        } else {
            $booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_created',
                'notes' => "Task created: {$validated['name']}",
            ]);
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        // Users can update tasks assigned to them, or if they have booking access
        if ($task->assigned_to !== auth()->id()) {
            Gate::authorize('update', $task->booking);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $wasCompleted = $task->status === 'completed';
        $oldAssignee = $task->assigned_to;
        $oldDueDate = $task->due_date;

        if ($validated['status'] === 'completed' && !$wasCompleted) {
            $validated['completed_at'] = now();
        }

        // Track when task was assigned (if newly assigned)
        $newlyAssigned = isset($validated['assigned_to']) && $validated['assigned_to'] != $oldAssignee;
        if ($newlyAssigned) {
            $validated['assigned_at'] = $validated['assigned_to'] ? now() : null;
        }

        $task->update($validated);

        // Log task completion
        if ($validated['status'] === 'completed' && !$wasCompleted) {
            $task->booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_completed',
                'notes' => "Task completed: {$task->name}",
            ]);
        }

        // Log task assignment change
        if ($newlyAssigned) {
            $newAssignee = $validated['assigned_to'] ? User::find($validated['assigned_to']) : null;
            $oldAssigneeUser = $oldAssignee ? User::find($oldAssignee) : null;

            if ($oldAssigneeUser && $newAssignee) {
                // Reassignment
                $task->booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'task_assigned',
                    'notes' => "Task reassigned from {$oldAssigneeUser->name} to {$newAssignee->name}: {$task->name}",
                ]);
            } elseif ($newAssignee) {
                // New assignment
                $task->booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'task_assigned',
                    'notes' => "Task assigned to {$newAssignee->name}: {$task->name}",
                ]);
            } elseif ($oldAssigneeUser) {
                // Unassignment
                $task->booking->activityLogs()->create([
                    'user_id' => auth()->id(),
                    'action_type' => 'task_assigned',
                    'notes' => "Task unassigned from {$oldAssigneeUser->name}: {$task->name}",
                ]);
            }
        }

        // Log task due date change
        $newDueDate = isset($validated['due_date']) ? \Carbon\Carbon::parse($validated['due_date']) : null;
        $dueDateChanged = ($oldDueDate xor $newDueDate) || ($oldDueDate && $newDueDate && !$oldDueDate->equalTo($newDueDate));
        if ($dueDateChanged) {
            $oldDateStr = $oldDueDate ? $oldDueDate->format('M j, Y') : 'none';
            $newDateStr = $newDueDate ? $newDueDate->format('M j, Y') : 'none';
            $task->booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_updated',
                'notes' => "Task due date changed from {$oldDateStr} to {$newDateStr}: {$task->name}",
            ]);
        }

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('update', $task->booking);

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
