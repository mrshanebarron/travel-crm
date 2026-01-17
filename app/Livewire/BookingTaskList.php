<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class BookingTaskList extends Component
{
    public Booking $booking;
    public $showAddModal = false;
    public $showEditModal = false;
    public $showAssignModal = false;

    // Form fields
    public $taskName = '';
    public $taskDescription = '';
    public $taskDueDate = '';
    public $taskAssignedTo = null;

    // Edit task
    public $editingTaskId = null;
    public $editTaskName = '';
    public $editTaskDueDate = '';
    public $editTaskAssignedTo = null;

    // Assign task
    public $assigningTaskId = null;
    public $assignTaskUserId = null;

    protected $listeners = ['taskUpdated' => '$refresh'];

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function toggleTask(Task $task)
    {
        // Only the assigned user can mark a task as complete
        // (or mark it back to pending if they completed it)
        if ($task->assigned_to !== auth()->id()) {
            session()->flash('error', 'Only the assigned user can mark this task as complete.');
            return;
        }

        if ($task->status === 'completed') {
            $task->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);

            $this->booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_updated',
                'notes' => "Task \"{$task->name}\" marked as pending",
            ]);
            $this->dispatch('activityCreated');
        } else {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_completed',
                'notes' => "Task \"{$task->name}\" completed",
            ]);
            $this->dispatch('activityCreated');
        }

        $this->booking->refresh();
    }

    public function openAddModal()
    {
        $this->reset(['taskName', 'taskDescription', 'taskDueDate', 'taskAssignedTo']);
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
    }

    public function addTask()
    {
        $this->validate([
            'taskName' => 'required|string|max:255',
            'taskDescription' => 'nullable|string',
            'taskDueDate' => 'nullable|date',
            'taskAssignedTo' => 'nullable|exists:users,id',
        ]);

        $task = $this->booking->tasks()->create([
            'name' => $this->taskName,
            'description' => $this->taskDescription,
            'due_date' => $this->taskDueDate ?: null,
            'assigned_to' => $this->taskAssignedTo ?: null,
            'assigned_at' => $this->taskAssignedTo ? now() : null,
            'assigned_by' => $this->taskAssignedTo ? auth()->id() : null,
            'status' => 'pending',
        ]);

        $assignee = $this->taskAssignedTo ? User::find($this->taskAssignedTo)?->name : null;
        $this->booking->activityLogs()->create([
            'user_id' => auth()->id(),
            'action_type' => 'task_created',
            'notes' => "Task \"{$this->taskName}\" created" . ($assignee ? " and assigned to {$assignee}" : ''),
        ]);
        $this->dispatch('activityCreated');

        $this->reset(['taskName', 'taskDescription', 'taskDueDate', 'taskAssignedTo']);
        $this->showAddModal = false;
        $this->booking->refresh();
    }

    public function openEditModal(Task $task)
    {
        $this->editingTaskId = $task->id;
        $this->editTaskName = $task->name;
        $this->editTaskDueDate = $task->due_date?->format('Y-m-d');
        $this->editTaskAssignedTo = $task->assigned_to;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTaskId = null;
    }

    public function updateTask()
    {
        $this->validate([
            'editTaskName' => 'required|string|max:255',
            'editTaskDueDate' => 'nullable|date',
            'editTaskAssignedTo' => 'nullable|exists:users,id',
        ]);

        $task = Task::findOrFail($this->editingTaskId);
        $oldName = $task->name;

        $wasAssigned = $task->assigned_to;
        $newAssigned = $this->editTaskAssignedTo;

        $task->update([
            'name' => $this->editTaskName,
            'due_date' => $this->editTaskDueDate ?: null,
            'assigned_to' => $newAssigned ?: null,
            'assigned_at' => (!$wasAssigned && $newAssigned) ? now() : $task->assigned_at,
            'assigned_by' => (!$wasAssigned && $newAssigned) ? auth()->id() : $task->assigned_by,
        ]);

        // Log the update
        $changes = [];
        if ($oldName !== $this->editTaskName) {
            $changes[] = "renamed to \"{$this->editTaskName}\"";
        }
        if ($wasAssigned != $newAssigned) {
            $newAssigneeName = $newAssigned ? User::find($newAssigned)?->name : 'unassigned';
            $changes[] = "assigned to {$newAssigneeName}";
        }

        $this->booking->activityLogs()->create([
            'user_id' => auth()->id(),
            'action_type' => 'task_updated',
            'notes' => "Task \"{$oldName}\" updated" . (count($changes) ? ': ' . implode(', ', $changes) : ''),
        ]);
        $this->dispatch('activityCreated');

        $this->showEditModal = false;
        $this->editingTaskId = null;
        $this->booking->refresh();
    }

    public function openAssignModal(Task $task)
    {
        $this->assigningTaskId = $task->id;
        $this->assignTaskUserId = $task->assigned_to;
        $this->showAssignModal = true;
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->assigningTaskId = null;
    }

    public function assignTask()
    {
        $task = Task::findOrFail($this->assigningTaskId);

        $task->update([
            'assigned_to' => $this->assignTaskUserId ?: null,
            'assigned_at' => $this->assignTaskUserId ? now() : null,
            'assigned_by' => $this->assignTaskUserId ? auth()->id() : null,
        ]);

        $assigneeName = $this->assignTaskUserId ? User::find($this->assignTaskUserId)?->name : 'unassigned';
        $this->booking->activityLogs()->create([
            'user_id' => auth()->id(),
            'action_type' => 'task_assigned',
            'notes' => "Task \"{$task->name}\" assigned to {$assigneeName}",
        ]);
        $this->dispatch('activityCreated');

        $this->showAssignModal = false;
        $this->assigningTaskId = null;
        $this->booking->refresh();
    }

    public function deleteTask(Task $task)
    {
        $taskName = $task->name;
        $task->delete();

        $this->booking->activityLogs()->create([
            'user_id' => auth()->id(),
            'action_type' => 'task_updated',
            'notes' => "Task \"{$taskName}\" deleted",
        ]);
        $this->dispatch('activityCreated');

        $this->booking->refresh();
    }

    public function render()
    {
        return view('livewire.booking-task-list', [
            'tasks' => $this->booking->tasks()->with('assignedTo')->get()->sortBy([['status', 'asc'], ['due_date', 'asc']]),
            'users' => User::orderBy('name')->get(),
            'completedCount' => $this->booking->tasks()->where('status', 'completed')->count(),
            'pendingCount' => $this->booking->tasks()->where('status', '!=', 'completed')->count(),
        ]);
    }
}
