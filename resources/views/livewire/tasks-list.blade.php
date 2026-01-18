<div>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Tasks</h1>
            <p class="text-slate-500 text-sm sm:text-base">Track and manage booking tasks</p>
        </div>
        <x-action-button type="create" label="Create Task" wire:click="openCreateModal" />
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <!-- Search Input -->
        <div class="mb-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tasks, bookings, assignees..." class="w-full pl-10 pr-4 py-2 rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-4">
            <button wire:click="setFilter('open')" class="tab whitespace-nowrap {{ $filter === 'open' ? 'active' : '' }}">Open ({{ $counts['open'] }})</button>
            <button wire:click="setFilter('mine')" class="tab whitespace-nowrap {{ $filter === 'mine' ? 'active' : '' }}">Mine ({{ $counts['mine'] }})</button>
            <button wire:click="setFilter('assigned')" class="tab whitespace-nowrap {{ $filter === 'assigned' ? 'active' : '' }}">Tasks I Assigned ({{ $counts['assigned'] }})</button>
            <button wire:click="setFilter('overdue')" class="tab whitespace-nowrap {{ $filter === 'overdue' ? 'active' : '' }}">Overdue ({{ $counts['overdue'] }})</button>
            <button wire:click="setFilter('completed')" class="tab whitespace-nowrap {{ $filter === 'completed' ? 'active' : '' }}">Done ({{ $counts['completed'] }})</button>
        </div>

        <!-- Results Count -->
        <div class="mt-3 text-sm text-slate-500">
            {{ $tasks->count() }} task{{ $tasks->count() !== 1 ? 's' : '' }}
        </div>
    </div>

    <!-- Tasks -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($tasks as $task)
                <div class="block p-4 {{ $task['is_overdue'] ? 'bg-red-50' : '' }}" wire:key="mobile-task-{{ $task['id'] }}">
                    <a href="{{ $task['booking_id'] ? route('bookings.show', $task['booking_id']) : ($task['transfer_id'] ? route('transfers.show', $task['transfer_id']) : '#') }}" class="block hover:bg-orange-50 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">{{ $task['name'] }}</p>
                                <p class="text-sm text-orange-600 font-medium">{{ $task['booking_number'] }}</p>
                            </div>
                            @if($task['status'] === 'pending')
                                <span class="badge text-xs" style="background: #f1f5f9; color: #475569;">Pending</span>
                            @elseif($task['status'] === 'in_progress')
                                <span class="badge badge-info text-xs">In Progress</span>
                            @else
                                <span class="badge badge-success text-xs">Done</span>
                            @endif
                        </div>
                        @if($task['description'])
                            <p class="text-sm text-slate-500 mb-2">{{ Str::limit($task['description'], 80) }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                            @if($task['due_date'])
                                <span class="flex items-center gap-1 {{ $task['is_overdue'] ? 'text-red-600 font-medium' : '' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $task['due_date_formatted'] }}
                                    @if($task['is_overdue'])
                                        <span class="text-xs">(overdue)</span>
                                    @endif
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $task['assigned_to_name'] ?? 'Unassigned' }}
                            </span>
                        </div>
                    </a>
                    <div class="mt-3 flex items-center gap-2">
                        @if($task['status'] !== 'completed')
                            <x-action-button type="complete" size="sm" wire:click="completeTask({{ $task['id'] }})" class="flex-1 justify-center" />
                        @else
                            <x-action-button type="clear" size="sm" label="Reopen" wire:click="uncompleteTask({{ $task['id'] }})" class="flex-1 justify-center" />
                        @endif
                        <x-action-button type="delete" size="sm" wire:click="deleteTask({{ $task['id'] }})" wire:confirm="Delete this task?" />
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-slate-500">No tasks found</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block table-responsive">
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
                        <tr class="cursor-pointer hover:bg-slate-50 {{ $task['is_overdue'] ? 'bg-red-50' : '' }}" wire:key="desktop-task-{{ $task['id'] }}" onclick="window.location = '{{ $task['booking_id'] ? route('bookings.show', $task['booking_id']) : ($task['transfer_id'] ? route('transfers.show', $task['transfer_id']) : '#') }}'">
                            <td>
                                <div class="font-medium text-slate-900">{{ $task['name'] }}</div>
                                @if($task['description'])
                                    <div class="text-sm text-slate-500">{{ Str::limit($task['description'], 60) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="text-orange-600 font-medium">{{ $task['booking_number'] }}</span>
                            </td>
                            <td>
                                @if($task['due_date'])
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 {{ $task['is_overdue'] ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="{{ $task['is_overdue'] ? 'text-red-600 font-medium' : 'text-slate-900' }}">{{ $task['due_date_formatted'] }}</span>
                                        @if($task['is_overdue'])
                                            <span class="text-xs text-red-600 font-medium">(overdue)</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">No due date</span>
                                @endif
                            </td>
                            <td>
                                @if($task['status'] === 'pending')
                                    <span class="badge" style="background: #f1f5f9; color: #475569;">Pending</span>
                                @elseif($task['status'] === 'in_progress')
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
                                    <span class="text-slate-700">{{ $task['assigned_to_name'] ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td onclick="event.stopPropagation()">
                                <div class="flex items-center gap-2">
                                    @if($task['status'] !== 'completed')
                                        <x-action-button type="complete" size="xs" wire:click="completeTask({{ $task['id'] }})" />
                                    @else
                                        <x-action-button type="clear" size="xs" label="Reopen" wire:click="uncompleteTask({{ $task['id'] }})" />
                                    @endif
                                    <x-action-button type="delete" size="xs" wire:click="deleteTask({{ $task['id'] }})" wire:confirm="Delete this task?" />
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
        </div>
    </div>

    <!-- Create Task Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="closeCreateModal">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Task</h3>
                <button type="button" wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form wire:submit="createTask">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Booking <span class="text-red-500">*</span></label>
                        <select wire:model="createBookingId" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Select a booking...</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}">{{ $booking->booking_number }} - {{ $booking->lead_traveler_name }} ({{ $booking->start_date->format('M j, Y') }})</option>
                            @endforeach
                        </select>
                        @error('createBookingId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Task Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="createName" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" placeholder="e.g., Collect passport copies">
                        @error('createName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea wire:model="createDescription" rows="2" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" placeholder="Additional details..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Assign To</label>
                            <select wire:model="createAssignedTo" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Due Date</label>
                            <input type="date" wire:model="createDueDate" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                    <x-action-button type="cancel" wire:click="closeCreateModal" />
                    <x-action-button type="create" label="Create Task" :submit="true" />
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
