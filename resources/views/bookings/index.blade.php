<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Bookings</h1>
            <p class="text-slate-500">Manage all safari bookings</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Booking
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('bookings.index') }}" class="tab {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('bookings.index') }}?status=upcoming" class="tab {{ request('status') === 'upcoming' ? 'active' : '' }}">Upcoming</a>
            <a href="{{ route('bookings.index') }}?status=active" class="tab {{ request('status') === 'active' ? 'active' : '' }}">Active</a>
            <a href="{{ route('bookings.index') }}?status=completed" class="tab {{ request('status') === 'completed' ? 'active' : '' }}">Completed</a>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking #</th>
                    <th>Start Date</th>
                    <th>Lead Traveler</th>
                    <th>Travelers</th>
                    <th>End Date</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $lead = $booking->travelers->where('is_lead', true)->first();
                        $totalTravelers = $booking->travelers->count();
                    @endphp
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td>
                            <a href="{{ route('bookings.show', $booking) }}" class="text-teal-600 hover:text-teal-700 font-medium">
                                {{ $booking->booking_number }}
                            </a>
                        </td>
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
                            @if($booking->status === 'upcoming')
                                <span class="badge badge-info">Upcoming</span>
                            @elseif($booking->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge" style="background: #f1f5f9; color: #475569;">Completed</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary text-sm py-2 px-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Open
                                </a>
                                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary text-sm py-2 px-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            No bookings found. <a href="{{ route('bookings.create') }}" class="text-teal-600 hover:text-teal-700">Create your first booking</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($bookings->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
