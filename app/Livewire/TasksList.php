<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class TasksList extends Component
{
    public $filter = 'open';
    public $search = '';

    // Create task form
    public $showCreateModal = false;
    public $createBookingId = '';
    public $createName = '';
    public $createDescription = '';
    public $createAssignedTo = '';
    public $createDueDate = '';

    protected $queryString = ['filter'];

    public function mount()
    {
        $this->filter = request('filter', 'open');
    }

    public function completeTask(Task $task)
    {
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function uncompleteTask(Task $task)
    {
        $task->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
    }

    public function openCreateModal()
    {
        $this->reset(['createBookingId', 'createName', 'createDescription', 'createAssignedTo', 'createDueDate']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function createTask()
    {
        $this->validate([
            'createBookingId' => 'required|exists:bookings,id',
            'createName' => 'required|string|max:255',
            'createDescription' => 'nullable|string',
            'createAssignedTo' => 'nullable|exists:users,id',
            'createDueDate' => 'nullable|date',
        ]);

        Task::create([
            'booking_id' => $this->createBookingId,
            'name' => $this->createName,
            'description' => $this->createDescription,
            'assigned_to' => $this->createAssignedTo ?: null,
            'assigned_at' => $this->createAssignedTo ? now() : null,
            'assigned_by' => $this->createAssignedTo ? auth()->id() : null,
            'due_date' => $this->createDueDate ?: null,
            'status' => 'pending',
        ]);

        $this->closeCreateModal();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function render()
    {
        $query = Task::with(['booking', 'assignedTo']);

        // Base filtering
        $currentUserId = auth()->id();

        $tasks = $query->get()->map(function ($task) use ($currentUserId) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'due_date_formatted' => $task->due_date?->format('M j, Y'),
                'is_overdue' => $task->due_date && $task->due_date->isPast() && $task->status !== 'completed',
                'booking_id' => $task->booking_id,
                'booking_number' => $task->booking?->booking_number ?? 'N/A',
                'assigned_to' => $task->assigned_to,
                'assigned_to_name' => $task->assignedTo?->name,
                'assigned_by' => $task->assigned_by,
            ];
        });

        // Apply filter
        $filteredTasks = $tasks->filter(function ($task) use ($currentUserId) {
            switch ($this->filter) {
                case 'open':
                    return $task['status'] !== 'completed';
                case 'mine':
                    return $task['assigned_to'] === $currentUserId && $task['status'] !== 'completed';
                case 'assigned':
                    return $task['assigned_by'] === $currentUserId && $task['assigned_to'] !== $currentUserId && $task['status'] !== 'completed';
                case 'overdue':
                    return $task['is_overdue'];
                case 'completed':
                    return $task['status'] === 'completed';
                default:
                    return true;
            }
        });

        // Apply search
        if ($this->search) {
            $searchLower = strtolower($this->search);
            $filteredTasks = $filteredTasks->filter(function ($task) use ($searchLower) {
                return str_contains(strtolower($task['name']), $searchLower) ||
                    str_contains(strtolower($task['description'] ?? ''), $searchLower) ||
                    str_contains(strtolower($task['booking_number']), $searchLower) ||
                    str_contains(strtolower($task['assigned_to_name'] ?? ''), $searchLower);
            });
        }

        // Sort by due date (nulls last), then by overdue first
        $filteredTasks = $filteredTasks->sortBy([
            fn ($a, $b) => ($b['is_overdue'] ?? false) <=> ($a['is_overdue'] ?? false),
            fn ($a, $b) => ($a['due_date'] ?? '9999-99-99') <=> ($b['due_date'] ?? '9999-99-99'),
        ])->values();

        return view('livewire.tasks-list', [
            'tasks' => $filteredTasks,
            'bookings' => Booking::orderBy('start_date', 'desc')->get(),
            'users' => User::orderBy('name')->get(),
            'counts' => [
                'open' => $tasks->where('status', '!=', 'completed')->count(),
                'mine' => $tasks->where('assigned_to', $currentUserId)->where('status', '!=', 'completed')->count(),
                'assigned' => $tasks->where('assigned_by', $currentUserId)->where('assigned_to', '!=', $currentUserId)->where('status', '!=', 'completed')->count(),
                'overdue' => $tasks->where('is_overdue', true)->count(),
                'completed' => $tasks->where('status', 'completed')->count(),
            ],
        ]);
    }
}
