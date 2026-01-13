<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Booking;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['booking', 'assignedTo', 'assignedBy']);

        // Apply filters
        switch ($request->filter) {
            case 'mine':
                $query->where('assigned_to', auth()->id())
                      ->where('status', '!=', 'completed');
                break;
            case 'assigned':
                $query->where('assigned_by', auth()->id())
                      ->where('assigned_to', '!=', auth()->id())
                      ->where('status', '!=', 'completed');
                break;
            case 'overdue':
                $query->where('due_date', '<', now())
                      ->where('status', '!=', 'completed');
                break;
            default:
                $query->where('status', '!=', 'completed');
                break;
        }

        $tasks = $query->orderBy('due_date')->paginate(20);

        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'days_before_safari' => 'nullable|integer|min:0',
        ]);

        $booking->tasks()->create([
            ...$validated,
            'assigned_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $task->update($validated);

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
