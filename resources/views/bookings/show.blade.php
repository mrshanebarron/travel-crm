<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $booking->booking_number }}
                </h2>
                <p class="text-sm text-gray-500">{{ $booking->country }} | {{ $booking->start_date->format('M j') }} - {{ $booking->end_date->format('M j, Y') }}</p>
            </div>
            <div class="flex gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $booking->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $booking->status === 'upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $booking->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ ucfirst($booking->status) }}
                </span>
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Travelers -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Travelers</h3>
                            @foreach($booking->groups as $group)
                                <div class="mb-4">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Group {{ $group->group_number }}</div>
                                    @foreach($group->travelers as $traveler)
                                        <div class="p-4 border rounded-lg mb-2 {{ $traveler->is_lead ? 'border-indigo-200 bg-indigo-50' : '' }}">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        {{ $traveler->full_name }}
                                                        @if($traveler->is_lead)
                                                            <span class="text-xs text-indigo-600 ml-2">(Lead)</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        @if($traveler->email) {{ $traveler->email }} @endif
                                                        @if($traveler->phone) | {{ $traveler->phone }} @endif
                                                    </div>
                                                    @if($traveler->dob)
                                                        <div class="text-sm text-gray-500">DOB: {{ $traveler->dob->format('M j, Y') }}</div>
                                                    @endif
                                                </div>
                                                <div class="text-right text-sm">
                                                    @if($traveler->arrivalFlight())
                                                        <div class="text-gray-600">
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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Safari Itinerary</h3>
                            <div class="space-y-3">
                                @foreach($booking->safariDays as $day)
                                    <div class="p-4 border rounded-lg">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    Day {{ $day->day_number }} - {{ $day->date->format('M j, Y') }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $day->location ?: 'Location TBD' }}
                                                    @if($day->lodge) | {{ $day->lodge }} @endif
                                                </div>
                                            </div>
                                            <div class="text-right text-xs text-gray-500">
                                                @if($day->meal_plan) {{ $day->meal_plan }} @endif
                                            </div>
                                        </div>
                                        @if($day->morning_activity || $day->afternoon_activity)
                                            <div class="mt-2 text-sm text-gray-500">
                                                @if($day->morning_activity) <span>AM: {{ $day->morning_activity }}</span> @endif
                                                @if($day->afternoon_activity) <span class="ml-4">PM: {{ $day->afternoon_activity }}</span> @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Ledger -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Ledger</h3>
                            </div>

                            <!-- Add Entry Form -->
                            <form method="POST" action="{{ route('ledger-entries.store', $booking) }}" class="mb-4 p-4 bg-gray-50 rounded-lg">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="rounded-md border-gray-300 text-sm" required>
                                    <input type="text" name="description" placeholder="Description" class="rounded-md border-gray-300 text-sm" required>
                                    <select name="type" class="rounded-md border-gray-300 text-sm" required>
                                        <option value="received">Received</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                    <input type="number" name="amount" placeholder="Amount" step="0.01" min="0" class="rounded-md border-gray-300 text-sm" required>
                                    <button type="submit" class="bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Add Entry</button>
                                </div>
                            </form>

                            @if($booking->ledgerEntries->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Received</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($booking->ledgerEntries as $entry)
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-900">{{ $entry->date->format('M j') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900">{{ $entry->description }}</td>
                                                <td class="px-3 py-2 text-sm text-right text-green-600">
                                                    {{ $entry->type === 'received' ? '$' . number_format($entry->amount, 2) : '' }}
                                                </td>
                                                <td class="px-3 py-2 text-sm text-right text-red-600">
                                                    {{ $entry->type === 'paid' ? '$' . number_format($entry->amount, 2) : '' }}
                                                </td>
                                                <td class="px-3 py-2 text-sm text-right font-medium {{ $entry->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    ${{ number_format($entry->balance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 text-center py-4">No ledger entries yet</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Tasks -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasks</h3>

                            <!-- Add Task Form -->
                            <form method="POST" action="{{ route('tasks.store', $booking) }}" class="mb-4">
                                @csrf
                                <div class="space-y-2">
                                    <input type="text" name="name" placeholder="New task..." class="w-full rounded-md border-gray-300 text-sm" required>
                                    <input type="date" name="due_date" class="w-full rounded-md border-gray-300 text-sm">
                                    <button type="submit" class="w-full bg-indigo-600 text-white rounded-md py-2 text-sm hover:bg-indigo-700">Add Task</button>
                                </div>
                            </form>

                            @forelse($booking->tasks->where('status', '!=', 'completed') as $task)
                                <div class="p-3 border rounded-lg mb-2">
                                    <div class="flex justify-between items-start">
                                        <div class="font-medium text-gray-900 text-sm">{{ $task->name }}</div>
                                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="name" value="{{ $task->name }}">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="text-green-600 hover:text-green-800 text-xs">Done</button>
                                        </form>
                                    </div>
                                    @if($task->due_date)
                                        <div class="text-xs {{ $task->due_date->isPast() ? 'text-red-600' : 'text-gray-500' }}">
                                            Due: {{ $task->due_date->format('M j') }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center">No pending tasks</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Rooms -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rooms</h3>

                            <!-- Add Room Form -->
                            <form method="POST" action="{{ route('rooms.store', $booking) }}" class="mb-4">
                                @csrf
                                <div class="space-y-2">
                                    <select name="type" class="w-full rounded-md border-gray-300 text-sm" required>
                                        <option value="double">Double</option>
                                        <option value="single">Single</option>
                                        <option value="triple">Triple</option>
                                        <option value="family">Family</option>
                                    </select>
                                    <input type="number" name="adults" placeholder="Adults" min="0" value="2" class="w-full rounded-md border-gray-300 text-sm" required>
                                    <button type="submit" class="w-full bg-indigo-600 text-white rounded-md py-2 text-sm hover:bg-indigo-700">Add Room</button>
                                </div>
                            </form>

                            @forelse($booking->rooms as $room)
                                <div class="p-3 border rounded-lg mb-2 flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm">{{ $room->display_type }}</div>
                                        <div class="text-xs text-gray-500">{{ $room->adults }} adults</div>
                                    </div>
                                    <form method="POST" action="{{ route('rooms.destroy', $room) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs">Remove</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center">No rooms added</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents</h3>

                            <!-- Upload Form -->
                            <form method="POST" action="{{ route('documents.store', $booking) }}" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="space-y-2">
                                    <input type="text" name="name" placeholder="Document name" class="w-full rounded-md border-gray-300 text-sm" required>
                                    <select name="category" class="w-full rounded-md border-gray-300 text-sm" required>
                                        <option value="flight">Flight</option>
                                        <option value="lodge">Lodge</option>
                                        <option value="passport">Passport</option>
                                        <option value="misc">Other</option>
                                    </select>
                                    <input type="file" name="file" class="w-full text-sm" required>
                                    <button type="submit" class="w-full bg-indigo-600 text-white rounded-md py-2 text-sm hover:bg-indigo-700">Upload</button>
                                </div>
                            </form>

                            @forelse($booking->documents as $doc)
                                <div class="p-3 border rounded-lg mb-2 flex justify-between items-center">
                                    <div>
                                        <a href="{{ route('documents.download', $doc) }}" class="font-medium text-indigo-600 hover:text-indigo-800 text-sm">
                                            {{ $doc->name }}
                                        </a>
                                        <div class="text-xs text-gray-500">{{ ucfirst($doc->category) }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center">No documents</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Activity Log -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Notes</h3>

                            <form method="POST" action="{{ route('activity-logs.store', $booking) }}" class="mb-4">
                                @csrf
                                <div class="space-y-2">
                                    <textarea name="notes" rows="2" placeholder="Add a note..." class="w-full rounded-md border-gray-300 text-sm" required></textarea>
                                    <button type="submit" class="w-full bg-indigo-600 text-white rounded-md py-2 text-sm hover:bg-indigo-700">Add Note</button>
                                </div>
                            </form>

                            @forelse($booking->activityLogs->sortByDesc('created_at')->take(5) as $log)
                                <div class="p-3 border-l-2 border-gray-200 mb-2">
                                    <div class="text-sm text-gray-900">{{ $log->notes }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $log->user->name }} - {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center">No activity notes</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
