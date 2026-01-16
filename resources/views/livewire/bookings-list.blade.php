<div>
    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <div class="flex items-center gap-2 sm:gap-4 overflow-x-auto pb-1 -mb-1">
            <button wire:click="setStatus('')" class="tab {{ !$status ? 'active' : '' }}">All</button>
            <button wire:click="setStatus('upcoming')" class="tab {{ $status === 'upcoming' ? 'active' : '' }}">Upcoming</button>
            <button wire:click="setStatus('active')" class="tab {{ $status === 'active' ? 'active' : '' }}">Active</button>
            <button wire:click="setStatus('completed')" class="tab {{ $status === 'completed' ? 'active' : '' }}">Completed</button>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    @if(count($selected) > 0)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 sm:p-4 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <span class="text-orange-700 font-medium text-sm sm:text-base">{{ count($selected) }} selected</span>
                <button type="button" wire:click="clearSelection" class="text-orange-600 hover:text-orange-800 text-sm underline">Clear</button>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('bookings.bulk-export') }}">
                    @csrf
                    <input type="hidden" name="booking_ids" value="{{ implode(',', $selected) }}">
                    <x-action-button type="export" size="sm" label="Export Selected" :submit="true" />
                </form>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center font-medium rounded border transition-colors text-xs py-1 px-2 gap-1 bg-slate-600 border-slate-600 text-white hover:bg-slate-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="hidden sm:inline">Change Status</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-10">
                        <button type="button" wire:click="bulkUpdateStatus('upcoming')" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Mark Upcoming</button>
                        <button type="button" wire:click="bulkUpdateStatus('active')" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Mark Active</button>
                        <button type="button" wire:click="bulkUpdateStatus('completed')" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Mark Completed</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bookings -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($bookings as $booking)
                @php
                    $lead = $booking->travelers->where('is_lead', true)->first();
                    $totalTravelers = $booking->travelers->count();
                @endphp
                <div class="p-4" wire:key="mobile-booking-{{ $booking->id }}">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" wire:model.live="selected" value="{{ $booking->id }}"
                            class="mt-1 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        <a href="{{ route('bookings.show', $booking) }}" class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate">
                                        {{ $lead ? $lead->last_name . ', ' . $lead->first_name : 'No lead traveler' }}
                                    </p>
                                    <p class="text-sm text-orange-600 font-medium">{{ $booking->booking_number }}</p>
                                </div>
                                @if($booking->status === 'upcoming')
                                    <span class="badge badge-info text-xs flex-shrink-0">Upcoming</span>
                                @elseif($booking->status === 'active')
                                    <span class="badge badge-success text-xs flex-shrink-0">Active</span>
                                @else
                                    <span class="badge text-xs flex-shrink-0" style="background: #f1f5f9; color: #475569;">Completed</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $totalTravelers }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $booking->country }}
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="flex items-center gap-2 mt-3 ml-7">
                        <x-action-button type="view" size="sm" :href="route('bookings.show', $booking)" class="flex-1 justify-center" />
                        <x-action-button type="edit" size="sm" :href="route('bookings.edit', $booking)" class="flex-1 justify-center" />
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 mb-4">No bookings found</p>
                    <a href="{{ route('bookings.create') }}" class="text-orange-600 hover:text-orange-800 font-medium">Create your first booking</a>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-12">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        </th>
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
                        <tr class="hover:bg-slate-50" wire:key="desktop-booking-{{ $booking->id }}">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $booking->id }}"
                                    class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                            </td>
                            <td class="cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                <span class="text-orange-600 font-medium">
                                    {{ $booking->booking_number }}
                                </span>
                            </td>
                            <td class="cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $booking->start_date->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                <span class="font-medium text-slate-900">
                                    {{ $lead ? $lead->last_name . ', ' . $lead->first_name : '-' }}
                                </span>
                            </td>
                            <td class="cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $totalTravelers }}
                                </div>
                            </td>
                            <td class="cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">{{ $booking->end_date->format('M d, Y') }}</td>
                            <td class="cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $booking->country }}
                                </div>
                            </td>
                            <td class="cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                @if($booking->status === 'upcoming')
                                    <span class="badge badge-info">Upcoming</span>
                                @elseif($booking->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge" style="background: #f1f5f9; color: #475569;">Completed</span>
                                @endif
                            </td>
                            <td onclick="event.stopPropagation()">
                                <div class="flex items-center gap-2">
                                    <x-action-button type="view" size="xs" :href="route('bookings.show', $booking)" />
                                    <x-action-button type="edit" size="xs" :href="route('bookings.edit', $booking)" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">
                                No bookings found. <a href="{{ route('bookings.create') }}" class="text-orange-600 hover:text-orange-800">Create your first booking</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-slate-200">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
