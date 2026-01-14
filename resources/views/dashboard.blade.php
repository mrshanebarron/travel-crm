<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-slate-500">Welcome back, {{ Auth::user()->name }}. Here is your booking overview.</p>
    </div>

    <!-- Stats Cards - 5 columns -->
    <div class="grid grid-cols-5 gap-4 mb-8">
        <a href="{{ route('bookings.index') }}?status=upcoming" class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['upcoming_bookings'] }}</p>
            <p class="text-sm text-slate-500">Upcoming Bookings</p>
        </a>

        <a href="{{ route('bookings.index') }}?status=active" class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['active_bookings'] }}</p>
            <p class="text-sm text-slate-500">Currently Running</p>
        </a>

        <a href="{{ route('bookings.index') }}?status=completed" class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['completed_bookings'] }}</p>
            <p class="text-sm text-slate-500">Past Bookings</p>
        </a>

        <a href="{{ route('tasks.index') }}?filter=mine" class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['tasks_assigned_to_me'] }}</p>
            <p class="text-sm text-slate-500">Tasks Assigned to Me</p>
        </a>

        <a href="{{ route('tasks.index') }}?filter=assigned" class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['tasks_assigned_by_me'] }}</p>
            <p class="text-sm text-slate-500">Tasks I Assigned</p>
        </a>
    </div>

    <!-- Upcoming Bookings Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Upcoming Bookings</h2>
            <a href="{{ route('bookings.index') }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium flex items-center gap-1">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Start Date</th>
                    <th>Lead Traveler</th>
                    <th>Travelers</th>
                    <th>End Date</th>
                    <th>Country</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcomingBookings as $booking)
                    @php
                        $lead = $booking->travelers->where('is_lead', true)->first();
                        $totalTravelers = $booking->travelers->count();
                    @endphp
                    <tr class="cursor-pointer hover:bg-slate-50" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                        <td>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $booking->start_date->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            <span class="font-medium text-slate-900">
                                {{ $lead ? $lead->last_name . ', ' . $lead->first_name : '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ $totalTravelers }}
                            </div>
                        </td>
                        <td>{{ $booking->end_date->format('M d, Y') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $booking->country }}
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary text-sm py-2 px-3" onclick="event.stopPropagation()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Open
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            No upcoming bookings
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900">New Booking</h3>
            </div>
            <p class="text-sm text-slate-500 mb-4">Create a new safari booking with client details.</p>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary w-full">Create Booking</a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900">New Transfer</h3>
            </div>
            <p class="text-sm text-slate-500 mb-4">Create a new fund transfer request to Kenya.</p>
            <a href="{{ route('transfers.create') }}" class="btn btn-primary w-full">Create Transfer</a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900">View Tasks</h3>
            </div>
            <p class="text-sm text-slate-500 mb-4">Manage pending tasks and assignments.</p>
            <a href="{{ route('tasks.index') }}" class="btn btn-primary w-full">View Tasks</a>
        </div>
    </div>
</x-app-layout>
