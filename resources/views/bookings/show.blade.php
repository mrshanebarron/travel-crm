<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('bookings.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $booking->booking_number }}</h1>
                <p class="text-slate-500">{{ $booking->country }} | {{ $booking->start_date->format('M j') }} - {{ $booking->end_date->format('M j, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($booking->status === 'upcoming')
                <span class="badge badge-info">Upcoming</span>
            @elseif($booking->status === 'active')
                <span class="badge badge-success">Active</span>
            @else
                <span class="badge" style="background: #f1f5f9; color: #475569;">Completed</span>
            @endif
            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Booking
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Travelers -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Travelers</h2>
                </div>
                <div class="p-6">
                    @foreach($booking->groups as $group)
                        <div class="mb-4">
                            <div class="text-sm font-medium text-slate-500 mb-2">Group {{ $group->group_number }}</div>
                            @foreach($group->travelers as $traveler)
                                <div class="p-4 border border-slate-200 rounded-lg mb-2 {{ $traveler->is_lead ? 'border-teal-200 bg-teal-50' : '' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium text-slate-900">
                                                {{ $traveler->full_name }}
                                                @if($traveler->is_lead)
                                                    <span class="text-xs text-teal-600 ml-2 font-semibold">(Lead)</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                @if($traveler->email) {{ $traveler->email }} @endif
                                                @if($traveler->phone) | {{ $traveler->phone }} @endif
                                            </div>
                                            @if($traveler->dob)
                                                <div class="text-sm text-slate-500">DOB: {{ $traveler->dob->format('M j, Y') }}</div>
                                            @endif
                                        </div>
                                        <div class="text-right text-sm">
                                            @if($traveler->arrivalFlight())
                                                <div class="text-slate-600">
                                                    Arrival: {{ $traveler->arrivalFlight()->airport }}
                                                    {{ $traveler->arrivalFlight()->date->format('M j') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Safari Itinerary -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Safari Itinerary</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($booking->safariDays as $day)
                            <div class="p-4 border border-slate-200 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-slate-900">
                                            Day {{ $day->day_number }} - {{ $day->date->format('M j, Y') }}
                                        </div>
                                        <div class="text-sm text-slate-600">
                                            {{ $day->location ?: 'Location TBD' }}
                                            @if($day->lodge) | {{ $day->lodge }} @endif
                                        </div>
                                    </div>
                                    <div class="text-right text-xs text-slate-500">
                                        @if($day->meal_plan)
                                            <span class="badge badge-info">{{ $day->meal_plan }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($day->morning_activity || $day->afternoon_activity)
                                    <div class="mt-2 text-sm text-slate-500">
                                        @if($day->morning_activity) <span>AM: {{ $day->morning_activity }}</span> @endif
                                        @if($day->afternoon_activity) <span class="ml-4">PM: {{ $day->afternoon_activity }}</span> @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-slate-500 text-center py-4">No itinerary days added yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Ledger -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Ledger</h2>
                </div>
                <div class="p-6">
                    <!-- Add Entry Form -->
                    <form method="POST" action="{{ route('ledger-entries.store', $booking) }}" class="mb-4 p-4 bg-slate-50 rounded-lg">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <input type="text" name="description" placeholder="Description" class="rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <select name="type" class="rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                                <option value="received">Received</option>
                                <option value="paid">Paid</option>
                            </select>
                            <input type="number" name="amount" placeholder="Amount" step="0.01" min="0" class="rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <button type="submit" class="btn btn-primary text-sm">Add Entry</button>
                        </div>
                    </form>

                    @if($booking->ledgerEntries->count() > 0)
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-right">Received</th>
                                    <th class="text-right">Paid</th>
                                    <th class="text-right">Balance</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->ledgerEntries as $entry)
                                    <tr>
                                        <td class="text-slate-900">{{ $entry->date->format('M j') }}</td>
                                        <td class="text-slate-900">{{ $entry->description }}</td>
                                        <td class="text-right text-green-600 font-medium">
                                            {{ $entry->type === 'received' ? '$' . number_format($entry->amount, 2) : '' }}
                                        </td>
                                        <td class="text-right text-red-600 font-medium">
                                            {{ $entry->type === 'paid' ? '$' . number_format($entry->amount, 2) : '' }}
                                        </td>
                                        <td class="text-right font-semibold {{ $entry->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format($entry->balance, 2) }}
                                        </td>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('ledger-entries.destroy', $entry) }}" class="inline" onsubmit="return confirm('Delete this entry?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-slate-500 text-center py-4">No ledger entries yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Tasks -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Tasks</h3>
                </div>
                <div class="p-6">
                    <!-- Add Task Form -->
                    <form method="POST" action="{{ route('tasks.store', $booking) }}" class="mb-4">
                        @csrf
                        <div class="space-y-2">
                            <input type="text" name="name" placeholder="New task..." class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <input type="date" name="due_date" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                            <button type="submit" class="btn btn-primary w-full">Add Task</button>
                        </div>
                    </form>

                    @forelse($booking->tasks->where('status', '!=', 'completed') as $task)
                        <div class="p-3 border border-slate-200 rounded-lg mb-2">
                            <div class="flex justify-between items-start">
                                <div class="font-medium text-slate-900 text-sm">{{ $task->name }}</div>
                                <form method="POST" action="{{ route('tasks.update', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="name" value="{{ $task->name }}">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-teal-600 hover:text-teal-800 text-xs font-medium">Done</button>
                                </form>
                            </div>
                            @if($task->due_date)
                                <div class="text-xs {{ $task->due_date->isPast() ? 'text-red-600 font-medium' : 'text-slate-500' }}">
                                    Due: {{ $task->due_date->format('M j') }}
                                    @if($task->due_date->isPast()) (overdue) @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-500 text-sm text-center">No pending tasks</p>
                    @endforelse
                </div>
            </div>

            <!-- Rooms -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Rooms</h3>
                </div>
                <div class="p-6">
                    <!-- Add Room Form -->
                    <form method="POST" action="{{ route('rooms.store', $booking) }}" class="mb-4">
                        @csrf
                        <div class="space-y-2">
                            <select name="type" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                                <option value="double">Double</option>
                                <option value="single">Single</option>
                                <option value="triple">Triple</option>
                                <option value="family">Family</option>
                            </select>
                            <input type="number" name="adults" placeholder="Adults" min="0" value="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <button type="submit" class="btn btn-primary w-full">Add Room</button>
                        </div>
                    </form>

                    @forelse($booking->rooms as $room)
                        <div class="p-3 border border-slate-200 rounded-lg mb-2 flex justify-between items-center">
                            <div>
                                <div class="font-medium text-slate-900 text-sm">{{ $room->display_type }}</div>
                                <div class="text-xs text-slate-500">{{ $room->adults }} adults</div>
                            </div>
                            <form method="POST" action="{{ route('rooms.destroy', $room) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Remove</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-slate-500 text-sm text-center">No rooms added</p>
                    @endforelse
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Documents</h3>
                </div>
                <div class="p-6">
                    <!-- Upload Form -->
                    <form method="POST" action="{{ route('documents.store', $booking) }}" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="space-y-2">
                            <input type="text" name="name" placeholder="Document name" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <select name="category" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                                <option value="flight">Flight</option>
                                <option value="lodge">Lodge</option>
                                <option value="passport">Passport</option>
                                <option value="misc">Other</option>
                            </select>
                            <input type="file" name="file" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" required>
                            <button type="submit" class="btn btn-primary w-full">Upload</button>
                        </div>
                    </form>

                    @forelse($booking->documents as $doc)
                        <div class="p-3 border border-slate-200 rounded-lg mb-2 flex justify-between items-center">
                            <div>
                                <a href="{{ route('documents.download', $doc) }}" class="font-medium text-teal-600 hover:text-teal-800 text-sm">
                                    {{ $doc->name }}
                                </a>
                                <div class="text-xs text-slate-500">{{ ucfirst($doc->category) }}</div>
                            </div>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Delete this document?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-slate-500 text-sm text-center">No documents</p>
                    @endforelse
                </div>
            </div>

            <!-- Activity Log -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Activity Notes</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('activity-logs.store', $booking) }}" class="mb-4">
                        @csrf
                        <div class="space-y-2">
                            <textarea name="notes" rows="2" placeholder="Add a note..." class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" required></textarea>
                            <button type="submit" class="btn btn-primary w-full">Add Note</button>
                        </div>
                    </form>

                    @forelse($booking->activityLogs->sortByDesc('created_at')->take(5) as $log)
                        <div class="p-3 border-l-2 border-teal-500 bg-slate-50 rounded-r-lg mb-2">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="text-sm text-slate-900">{{ $log->notes }}</div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $log->user->name }} - {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('activity-logs.destroy', $log) }}" onsubmit="return confirm('Delete this note?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium ml-2">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 text-sm text-center">No activity notes</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
