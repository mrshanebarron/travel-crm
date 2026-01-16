<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-slate-500 text-sm sm:text-base">Welcome back, {{ Auth::user()->name }}. Here is your booking overview.</p>
    </div>

    <!-- Stats Cards - Responsive grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <a href="{{ route('bookings.index') }}?status=upcoming" class="stat-card group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-orange-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-900 mb-0.5 sm:mb-1">{{ $stats['upcoming_bookings'] }}</p>
            <p class="text-xs sm:text-sm text-slate-500">Upcoming</p>
        </a>

        <a href="{{ route('bookings.index') }}?status=active" class="stat-card group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-green-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-900 mb-0.5 sm:mb-1">{{ $stats['active_bookings'] }}</p>
            <p class="text-xs sm:text-sm text-slate-500">Running</p>
        </a>

        <a href="{{ route('bookings.index') }}?status=completed" class="stat-card group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-slate-400"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-900 mb-0.5 sm:mb-1">{{ $stats['completed_bookings'] }}</p>
            <p class="text-xs sm:text-sm text-slate-500">Completed</p>
        </a>

        <a href="{{ route('tasks.index') }}?filter=mine" class="stat-card group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-amber-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-900 mb-0.5 sm:mb-1">{{ $stats['tasks_assigned_to_me'] }}</p>
            <p class="text-xs sm:text-sm text-slate-500">My Tasks</p>
        </a>

        <a href="{{ route('tasks.index') }}?filter=assigned" class="stat-card group col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-purple-500"></span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-900 mb-0.5 sm:mb-1">{{ $stats['tasks_assigned_by_me'] }}</p>
            <p class="text-xs sm:text-sm text-slate-500">Tasks I Assigned to Others</p>
        </a>
    </div>

    <!-- Upcoming Bookings -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-slate-900">Upcoming Bookings</h2>
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('bookings.index') }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium flex items-center gap-1">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
<x-action-button type="create" size="sm" label="New Booking" :href="route('bookings.create')" />
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden">
            @forelse($upcomingBookings as $booking)
                @php
                    $lead = $booking->travelers->where('is_lead', true)->first();
                    $totalTravelers = $booking->travelers->count();
                @endphp
                <a href="{{ route('bookings.show', $booking) }}" class="block p-4 border-b border-slate-100 hover:bg-orange-50 transition-colors">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $lead ? $lead->last_name . ', ' . $lead->first_name : 'No lead traveler' }}
                            </p>
                            <p class="text-sm text-orange-600 font-medium">{{ $booking->booking_number }}</p>
                        </div>
                        <span class="badge badge-info text-xs">{{ $totalTravelers }} travelers</span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-slate-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $booking->country }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 mb-4">No upcoming bookings</p>
                    <x-action-button type="create" label="Create Your First Booking" :href="route('bookings.create')" />
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block table-responsive">
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
                            <td onclick="event.stopPropagation()">
                                <x-action-button type="view" size="sm" :href="route('bookings.show', $booking)" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <p class="mb-4">No upcoming bookings</p>
                                <x-action-button type="create" label="Create Your First Booking" :href="route('bookings.create')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>
