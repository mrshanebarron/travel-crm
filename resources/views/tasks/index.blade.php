<x-app-layout>
    <div x-data="taskManager()" x-init="init()">
        <!-- Page Title -->
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Tasks</h1>
                <p class="text-slate-500 text-sm sm:text-base">Track and manage booking tasks</p>
            </div>
            <button type="button" onclick="document.getElementById('create-task-modal').classList.remove('hidden')" class="btn btn-primary text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Task
            </button>
        </div>

        <!-- Search and Filter Bar -->
        <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6">
            <!-- Search Input -->
            <div class="mb-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" x-model="search" placeholder="Search tasks, bookings, assignees..." class="w-full pl-10 pr-4 py-2 rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                <button @click="filter = 'open'" :class="filter === 'open' ? 'tab active' : 'tab'" class="whitespace-nowrap">Open</button>
                <button @click="filter = 'mine'" :class="filter === 'mine' ? 'tab active' : 'tab'" class="whitespace-nowrap">Mine</button>
                <button @click="filter = 'assigned'" :class="filter === 'assigned' ? 'tab active' : 'tab'" class="whitespace-nowrap">Tasks I Assigned</button>
                <button @click="filter = 'overdue'" :class="filter === 'overdue' ? 'tab active' : 'tab'" class="whitespace-nowrap">Overdue</button>
                <button @click="filter = 'completed'" :class="filter === 'completed' ? 'tab active' : 'tab'" class="whitespace-nowrap">Done</button>
            </div>

            <!-- Results Count -->
            <div class="mt-3 text-sm text-slate-500">
                <span x-text="filteredTasks.length"></span> task<span x-show="filteredTasks.length !== 1">s</span>
            </div>
        </div>

        <!-- Tasks -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-slate-100">
                <template x-for="task in filteredTasks" :key="task.id">
                    <a :href="'/bookings/' + task.booking_id" class="block p-4 hover:bg-orange-50 transition-colors" :class="task.is_overdue ? 'bg-red-50' : ''">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900" x-text="task.name"></p>
                                <p class="text-sm text-orange-600 font-medium" x-text="task.booking_number"></p>
                            </div>
                            <span x-show="task.status === 'pending'" class="badge text-xs" style="background: #f1f5f9; color: #475569;">Pending</span>
                            <span x-show="task.status === 'in_progress'" class="badge badge-info text-xs">In Progress</span>
                            <span x-show="task.status === 'completed'" class="badge badge-success text-xs">Done</span>
                        </div>
                        <p x-show="task.description" class="text-sm text-slate-500 mb-2" x-text="task.description?.substring(0, 80) + (task.description?.length > 80 ? '...' : '')"></p>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                            <span x-show="task.due_date" class="flex items-center gap-1" :class="task.is_overdue ? 'text-red-600 font-medium' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span x-text="task.due_date_formatted"></span>
                                <span x-show="task.is_overdue" class="text-xs">(overdue)</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span x-text="task.assigned_to_name || 'Unassigned'"></span>
                            </span>
                        </div>
                        <div class="mt-3 flex items-center gap-2" @click.stop.prevent>
                            <form x-show="task.status !== 'completed'" method="POST" :action="'/tasks/' + task.id" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="name" :value="task.name">
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-primary text-sm py-1.5 px-3 w-full justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Complete
                                </button>
                            </form>
                            <form method="POST" :action="'/tasks/' + task.id" onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary text-sm py-1.5 px-3 text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </a>
                </template>
                <div x-show="filteredTasks.length === 0" class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-slate-500">No tasks found</p>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="cursor-pointer hover:bg-slate-100" @click="toggleSort('name')">
                                <div class="flex items-center gap-1">
                                    Task
                                    <svg x-show="sortBy === 'name_asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    <svg x-show="sortBy === 'name_desc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </th>
                            <th class="cursor-pointer hover:bg-slate-100" @click="toggleSort('booking')">
                                <div class="flex items-center gap-1">
                                    Booking
                                    <svg x-show="sortBy === 'booking'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </div>
                            </th>
                            <th class="cursor-pointer hover:bg-slate-100" @click="toggleSort('due_date')">
                                <div class="flex items-center gap-1">
                                    Due Date
                                    <svg x-show="sortBy === 'due_date_asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    <svg x-show="sortBy === 'due_date_desc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </th>
                            <th class="cursor-pointer hover:bg-slate-100" @click="toggleSort('status')">
                                <div class="flex items-center gap-1">
                                    Status
                                    <svg x-show="sortBy === 'status'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </div>
                            </th>
                            <th class="cursor-pointer hover:bg-slate-100" @click="toggleSort('assigned_to')">
                                <div class="flex items-center gap-1">
                                    Assigned To
                                    <svg x-show="sortBy === 'assigned_to'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </div>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="task in filteredTasks" :key="task.id">
                            <tr class="cursor-pointer hover:bg-slate-50" :class="task.is_overdue ? 'bg-red-50' : ''" @click="window.location = '/bookings/' + task.booking_id">
                                <td>
                                    <div class="font-medium text-slate-900" x-text="task.name"></div>
                                    <div x-show="task.description" class="text-sm text-slate-500" x-text="task.description?.substring(0, 60) + (task.description?.length > 60 ? '...' : '')"></div>
                                </td>
                                <td>
                                    <span class="text-orange-600 font-medium" x-text="task.booking_number"></span>
                                </td>
                                <td>
                                    <template x-if="task.due_date">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" :class="task.is_overdue ? 'text-red-500' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span :class="task.is_overdue ? 'text-red-600 font-medium' : 'text-slate-900'" x-text="task.due_date_formatted"></span>
                                            <span x-show="task.is_overdue" class="text-xs text-red-600 font-medium">(overdue)</span>
                                        </div>
                                    </template>
                                    <template x-if="!task.due_date">
                                        <span class="text-slate-400">No due date</span>
                                    </template>
                                </td>
                                <td>
                                    <span x-show="task.status === 'pending'" class="badge" style="background: #f1f5f9; color: #475569;">Pending</span>
                                    <span x-show="task.status === 'in_progress'" class="badge badge-info">In Progress</span>
                                    <span x-show="task.status === 'completed'" class="badge badge-success">Completed</span>
                                </td>
                                <td @click.stop>
                                    <form method="POST" :action="'/tasks/' + task.id" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" :value="task.name">
                                        <input type="hidden" name="status" :value="task.status">
                                        <select name="assigned_to" onchange="this.form.submit()" class="text-sm border-0 bg-transparent p-0 pr-6 focus:ring-0 cursor-pointer" :class="task.assigned_to_name ? 'text-slate-700' : 'text-amber-600 font-medium'">
                                            <option value="">-- Assign --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" :selected="task.assigned_to == {{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td @click.stop>
                                    <div class="flex items-center gap-2">
                                        <form x-show="task.status !== 'completed'" method="POST" :action="'/tasks/' + task.id" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="name" :value="task.name">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-primary text-sm py-2 px-3">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Complete
                                            </button>
                                        </form>
                                        <form method="POST" :action="'/tasks/' + task.id" class="inline" onsubmit="return confirm('Delete this task?')">
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
                        </template>
                        <tr x-show="filteredTasks.length === 0">
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                No tasks found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function taskManager() {
            return {
                tasks: @json($tasksJson),
                search: '',
                filter: '{{ request('filter') ?: 'open' }}',
                sortBy: 'due_date_asc',
                currentUserId: {{ auth()->id() }},

                init() {
                    // Preserve initial filter from URL if present
                },

                get filteredTasks() {
                    let result = [...this.tasks];

                    // Apply filter
                    switch (this.filter) {
                        case 'open':
                            result = result.filter(t => t.status !== 'completed');
                            break;
                        case 'mine':
                            result = result.filter(t => t.assigned_to === this.currentUserId && t.status !== 'completed');
                            break;
                        case 'assigned':
                            result = result.filter(t => t.assigned_by === this.currentUserId && t.assigned_to !== this.currentUserId && t.status !== 'completed');
                            break;
                        case 'overdue':
                            result = result.filter(t => t.is_overdue);
                            break;
                        case 'completed':
                            result = result.filter(t => t.status === 'completed');
                            break;
                    }

                    // Apply search
                    if (this.search.trim()) {
                        const searchLower = this.search.toLowerCase();
                        result = result.filter(t =>
                            t.name.toLowerCase().includes(searchLower) ||
                            (t.description && t.description.toLowerCase().includes(searchLower)) ||
                            t.booking_number.toLowerCase().includes(searchLower) ||
                            (t.assigned_to_name && t.assigned_to_name.toLowerCase().includes(searchLower))
                        );
                    }

                    // Apply sort
                    result.sort((a, b) => {
                        switch (this.sortBy) {
                            case 'due_date_asc':
                                if (!a.due_date && !b.due_date) return 0;
                                if (!a.due_date) return 1;
                                if (!b.due_date) return -1;
                                return a.due_date.localeCompare(b.due_date);
                            case 'due_date_desc':
                                if (!a.due_date && !b.due_date) return 0;
                                if (!a.due_date) return 1;
                                if (!b.due_date) return -1;
                                return b.due_date.localeCompare(a.due_date);
                            case 'name_asc':
                                return a.name.localeCompare(b.name);
                            case 'name_desc':
                                return b.name.localeCompare(a.name);
                            case 'booking':
                                return a.booking_number.localeCompare(b.booking_number);
                            case 'assigned_to':
                                const aName = a.assigned_to_name || 'zzz';
                                const bName = b.assigned_to_name || 'zzz';
                                return aName.localeCompare(bName);
                            case 'status':
                                const statusOrder = { pending: 0, in_progress: 1, completed: 2 };
                                return statusOrder[a.status] - statusOrder[b.status];
                            default:
                                return 0;
                        }
                    });

                    return result;
                },

                toggleSort(field) {
                    if (field === 'due_date') {
                        this.sortBy = this.sortBy === 'due_date_asc' ? 'due_date_desc' : 'due_date_asc';
                    } else if (field === 'name') {
                        this.sortBy = this.sortBy === 'name_asc' ? 'name_desc' : 'name_asc';
                    } else {
                        this.sortBy = field;
                    }
                }
            }
        }
    </script>

    <!-- Create Task Modal -->
    <div id="create-task-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Create Task</h3>
                <button type="button" onclick="document.getElementById('create-task-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="create-task-form" method="POST" action="">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Booking <span class="text-red-500">*</span></label>
                        <select id="task-booking-select" name="booking_id" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" onchange="updateFormAction()">
                            <option value="">Select a booking...</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}">{{ $booking->booking_number }} - {{ $booking->lead_traveler_name }} ({{ $booking->start_date->format('M j, Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Task Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" placeholder="e.g., Collect passport copies">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" placeholder="Additional details..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Assign To</label>
                            <select name="assigned_to" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('create-task-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateFormAction() {
            const bookingId = document.getElementById('task-booking-select').value;
            const form = document.getElementById('create-task-form');
            if (bookingId) {
                form.action = `/bookings/${bookingId}/tasks`;
            } else {
                form.action = '';
            }
        }
    </script>
</x-app-layout>
