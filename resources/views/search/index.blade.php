<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Search Results</h1>
        <p class="text-slate-500">
            @if($query)
                Showing results for "{{ $query }}"
            @else
                Enter a search term to find bookings and travelers
            @endif
        </p>
    </div>

    @if(strlen($query) >= 2)
        <!-- Bookings Results -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Bookings ({{ $bookings->count() }})</h2>
            </div>

            @if($bookings->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Booking #</th>
                            <th>Lead Traveler</th>
                            <th>Country</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            @php
                                $lead = $booking->travelers->where('is_lead', true)->first();
                            @endphp
                            <tr class="cursor-pointer hover:bg-slate-50" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                <td>
                                    <span class="text-teal-600 font-medium">{{ $booking->booking_number }}</span>
                                </td>
                                <td>
                                    <span class="font-medium text-slate-900">
                                        {{ $lead ? $lead->last_name . ', ' . $lead->first_name : '-' }}
                                    </span>
                                </td>
                                <td>{{ $booking->country }}</td>
                                <td>{{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}</td>
                                <td>
                                    @if($booking->status === 'upcoming')
                                        <span class="badge badge-info">Upcoming</span>
                                    @elseif($booking->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge" style="background: #f1f5f9; color: #475569;">Completed</span>
                                    @endif
                                </td>
                                <td onclick="event.stopPropagation()">
                                    <x-action-button type="view" size="sm" :href="route('bookings.show', $booking)" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center text-slate-500">
                    No bookings found matching "{{ $query }}"
                </div>
            @endif
        </div>

        <!-- Travelers Results -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Travelers ({{ $travelers->count() }})</h2>
            </div>

            @if($travelers->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Booking</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($travelers as $traveler)
                            <tr class="cursor-pointer hover:bg-slate-50" onclick="window.location='{{ route('bookings.show', $traveler->group->booking) }}'">
                                <td>
                                    <span class="font-medium text-slate-900">
                                        {{ $traveler->last_name }}, {{ $traveler->first_name }}
                                        @if($traveler->is_lead)
                                            <span class="ml-2 text-xs text-teal-600">(Lead)</span>
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $traveler->email ?? '-' }}</td>
                                <td>{{ $traveler->phone ?? '-' }}</td>
                                <td>
                                    <span class="text-teal-600">{{ $traveler->group->booking->booking_number }}</span>
                                </td>
                                <td onclick="event.stopPropagation()">
                                    <x-action-button type="view" size="sm" label="View Booking" :href="route('bookings.show', $traveler->group->booking)" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center text-slate-500">
                    No travelers found matching "{{ $query }}"
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <p class="text-slate-500">Enter at least 2 characters to search</p>
        </div>
    @endif
</x-app-layout>
