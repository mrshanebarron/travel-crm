<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class TasksList extends Component
{
    public $filter = 'open';
    public $search = '';
    public $selectedDate = null; // For calendar date filtering
    public $calendarMonth; // Current month being viewed (Y-m format)

    // Create task form
    public $showCreateModal = false;
    public $createBookingId = '';
    public $createName = '';
    public $createDescription = '';
    public $createAssignedTo = '';
    public $createDueDate = '';

    protected $queryString = ['filter', 'selectedDate'];

    public function mount()
    {
        $this->filter = request('filter', 'open');
        $this->selectedDate = request('selectedDate');
        $this->calendarMonth = now()->format('Y-m');
    }

    public function selectDate($date)
    {
        // Toggle - if already selected, deselect
        if ($this->selectedDate === $date) {
            $this->selectedDate = null;
        } else {
            $this->selectedDate = $date;
        }
    }

    public function clearDateFilter()
    {
        $this->selectedDate = null;
    }

    public function previousMonth()
    {
        $this->calendarMonth = Carbon::parse($this->calendarMonth . '-01')->subMonth()->format('Y-m');
    }

    public function nextMonth()
    {
        $this->calendarMonth = Carbon::parse($this->calendarMonth . '-01')->addMonth()->format('Y-m');
    }

    public function goToToday()
    {
        $this->calendarMonth = now()->format('Y-m');
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function completeTask(Task $task)
    {
        // Only the assigned user can mark a task as complete
        if ($task->assigned_to !== auth()->id()) {
            session()->flash('error', 'Only the assigned user can mark this task as complete.');
            return;
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Log activity
        if ($task->booking) {
            $task->booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_completed',
                'notes' => "Task \"{$task->name}\" completed",
            ]);
        }
    }

    public function uncompleteTask(Task $task)
    {
        // Only the assigned user can unmark their completed task
        if ($task->assigned_to !== auth()->id()) {
            session()->flash('error', 'Only the assigned user can modify this task.');
            return;
        }

        $task->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        // Log activity
        if ($task->booking) {
            $task->booking->activityLogs()->create([
                'user_id' => auth()->id(),
                'action_type' => 'task_updated',
                'notes' => "Task \"{$task->name}\" marked as pending",
            ]);
        }
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
        // Get ALL assigned tasks (not filtered by date - calendar shows full picture)
        $query = Task::with(['booking', 'assignedTo', 'transfer'])
            ->where(function ($q) {
                $q->whereNotNull('assigned_to')  // Has an assignment
                  ->orWhere('status', 'completed');  // Or is completed
            })
            ->whereNotNull('due_date'); // Must have a due date to appear

        $currentUserId = auth()->id();

        $allTasks = $query->get()->map(function ($task) use ($currentUserId) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'due_date_formatted' => $task->due_date?->format('M j, Y'),
                'is_overdue' => $task->due_date && $task->due_date->isPast() && $task->status !== 'completed',
                'is_today' => $task->due_date && $task->due_date->isToday(),
                'is_future' => $task->due_date && $task->due_date->isFuture(),
                'booking_id' => $task->booking_id,
                'booking_number' => $task->booking?->booking_number ?? ($task->transfer ? $task->transfer->transfer_number : 'N/A'),
                'transfer_id' => $task->transfer_id,
                'assigned_to' => $task->assigned_to,
                'assigned_to_name' => $task->assignedTo?->name,
                'assigned_by' => $task->assigned_by,
            ];
        });

        // Build calendar data - task counts by date for the current month
        $calendarStart = Carbon::parse($this->calendarMonth . '-01')->startOfMonth();
        $calendarEnd = $calendarStart->copy()->endOfMonth();

        $tasksByDate = $allTasks
            ->where('status', '!=', 'completed')
            ->groupBy('due_date')
            ->map(fn ($tasks) => $tasks->count());

        // Build calendar weeks
        $calendarWeeks = [];
        $date = $calendarStart->copy()->startOfWeek(Carbon::SUNDAY);
        while ($date <= $calendarEnd->copy()->endOfWeek(Carbon::SATURDAY)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateStr = $date->format('Y-m-d');
                $week[] = [
                    'date' => $dateStr,
                    'day' => $date->day,
                    'isCurrentMonth' => $date->month === $calendarStart->month,
                    'isToday' => $date->isToday(),
                    'isPast' => $date->isPast() && !$date->isToday(),
                    'taskCount' => $tasksByDate[$dateStr] ?? 0,
                    'hasOverdue' => $date->isPast() && !$date->isToday() && ($tasksByDate[$dateStr] ?? 0) > 0,
                ];
                $date->addDay();
            }
            $calendarWeeks[] = $week;
        }

        // Apply status filter
        $tasks = $allTasks->filter(function ($task) use ($currentUserId) {
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

        // Apply date filter from calendar
        if ($this->selectedDate) {
            $tasks = $tasks->filter(fn ($task) => $task['due_date'] === $this->selectedDate);
        }

        // Apply search
        if ($this->search) {
            $searchLower = strtolower($this->search);
            $tasks = $tasks->filter(function ($task) use ($searchLower) {
                return str_contains(strtolower($task['name']), $searchLower) ||
                    str_contains(strtolower($task['description'] ?? ''), $searchLower) ||
                    str_contains(strtolower($task['booking_number']), $searchLower) ||
                    str_contains(strtolower($task['assigned_to_name'] ?? ''), $searchLower);
            });
        }

        // Sort by due date (overdue first, then today, then future)
        $tasks = $tasks->sortBy([
            fn ($a, $b) => ($b['is_overdue'] ?? false) <=> ($a['is_overdue'] ?? false),
            fn ($a, $b) => ($a['due_date'] ?? '9999-99-99') <=> ($b['due_date'] ?? '9999-99-99'),
        ])->values();

        return view('livewire.tasks-list', [
            'tasks' => $tasks,
            'bookings' => Booking::orderBy('start_date', 'desc')->get(),
            'users' => User::orderBy('name')->get(),
            'calendarWeeks' => $calendarWeeks,
            'calendarMonthName' => Carbon::parse($this->calendarMonth . '-01')->format('F Y'),
            'todayDate' => now()->format('Y-m-d'),
            'counts' => [
                'open' => $allTasks->where('status', '!=', 'completed')->count(),
                'mine' => $allTasks->where('assigned_to', $currentUserId)->where('status', '!=', 'completed')->count(),
                'assigned' => $allTasks->where('assigned_by', $currentUserId)->where('assigned_to', '!=', $currentUserId)->where('status', '!=', 'completed')->count(),
                'overdue' => $allTasks->where('is_overdue', true)->count(),
                'completed' => $allTasks->where('status', 'completed')->count(),
            ],
        ]);
    }
}
