<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tasks</h1>
            <p class="text-slate-500">Track and manage booking tasks</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('tasks.index') }}" class="tab {{ !request('filter') ? 'active' : '' }}">Open Tasks</a>
            <a href="{{ route('tasks.index') }}?filter=mine" class="tab {{ request('filter') === 'mine' ? 'active' : '' }}">Assigned to Me</a>
            <a href="{{ route('tasks.index') }}?filter=assigned" class="tab {{ request('filter') === 'assigned' ? 'active' : '' }}">Tasks I Assigned</a>
            <a href="{{ route('tasks.index') }}?filter=overdue" class="tab {{ request('filter') === 'overdue' ? 'active' : '' }}">Overdue</a>
            <a href="{{ route('tasks.index') }}?filter=completed" class="tab {{ request('filter') === 'completed' ? 'active' : '' }}">Completed</a>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Booking</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr class="cursor-pointer hover:bg-slate-50 {{ $task->due_date && $task->due_date->isPast() && $task->status !== 'completed' ? 'bg-red-50' : '' }}" onclick="window.location='{{ route('bookings.show', $task->booking) }}'">
                        <td>
                            <div class="font-medium text-slate-900">{{ $task->name }}</div>
                            @if($task->description)
                                <div class="text-sm text-slate-500">{{ Str::limit($task->description, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="text-teal-600 font-medium">
                                {{ $task->booking->booking_number }}
                            </span>
                        </td>
                        <td>
                            @if($task->due_date)
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600 font-medium' : 'text-slate-900' }}">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </span>
                                    @if($task->due_date->isPast() && $task->status !== 'completed')
                                        <span class="text-xs text-red-600 font-medium">(overdue)</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400">No due date</span>
                            @endif
                        </td>
                        <td>
                            @if($task->status === 'pending')
                                <span class="badge" style="background: #f1f5f9; color: #475569;">Pending</span>
                            @elseif($task->status === 'in_progress')
                                <span class="badge badge-info">In Progress</span>
                            @else
                                <span class="badge badge-success">Completed</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-slate-200 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="text-slate-700">{{ $task->assignedTo?->name ?? 'Unassigned' }}</span>
                            </div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="flex items-center gap-2">
                                @if($task->status !== 'completed')
                                    <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" value="{{ $task->name }}">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary text-sm py-2 px-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Complete
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Delete this task?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary text-sm py-2 px-3 text-red-600 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            No tasks found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($tasks->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $tasks->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
