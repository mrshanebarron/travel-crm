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

    <!-- Bulk Actions Bar (hidden by default) -->
    <div id="bulk-actions-bar" class="hidden bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="text-orange-700 font-medium"><span id="selected-count">0</span> selected</span>
            <button type="button" onclick="clearSelection()" class="text-orange-600 hover:text-orange-800 text-sm underline">Clear selection</button>
        </div>
        <div class="flex items-center gap-2">
            <form id="bulk-export-form" method="POST" action="{{ route('bookings.bulk-export') }}">
                @csrf
                <input type="hidden" name="booking_ids" id="bulk-export-ids">
                <button type="submit" class="btn btn-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Selected
                </button>
            </form>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="btn btn-secondary text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Change Status
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-10">
                    <form method="POST" action="{{ route('bookings.bulk-status') }}">
                        @csrf
                        <input type="hidden" name="booking_ids" class="bulk-ids-input">
                        <input type="hidden" name="status" value="upcoming">
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Mark Upcoming</button>
                    </form>
                    <form method="POST" action="{{ route('bookings.bulk-status') }}">
                        @csrf
                        <input type="hidden" name="booking_ids" class="bulk-ids-input">
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Mark Active</button>
                    </form>
                    <form method="POST" action="{{ route('bookings.bulk-status') }}">
                        @csrf
                        <input type="hidden" name="booking_ids" class="bulk-ids-input">
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Mark Completed</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">
                        <input type="checkbox" id="select-all" onchange="toggleAllSelection()"
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
                    <tr class="hover:bg-slate-50">
                        <td onclick="event.stopPropagation()">
                            <input type="checkbox" class="booking-checkbox rounded border-slate-300 text-orange-600 focus:ring-orange-500"
                                value="{{ $booking->id }}" onchange="updateSelection()">
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
                        <td colspan="9" class="py-12 text-center text-slate-500">
                            No bookings found. <a href="{{ route('bookings.create') }}" class="text-orange-600 hover:text-orange-800">Create your first booking</a>
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

    <script>
        let selectedBookings = new Set();

        function toggleAllSelection() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.booking-checkbox');

            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
                if (selectAll.checked) {
                    selectedBookings.add(cb.value);
                } else {
                    selectedBookings.delete(cb.value);
                }
            });

            updateBulkActionsBar();
        }

        function updateSelection() {
            selectedBookings.clear();
            document.querySelectorAll('.booking-checkbox:checked').forEach(cb => {
                selectedBookings.add(cb.value);
            });

            const checkboxes = document.querySelectorAll('.booking-checkbox');
            const selectAll = document.getElementById('select-all');
            selectAll.checked = checkboxes.length > 0 &&
                Array.from(checkboxes).every(cb => cb.checked);
            selectAll.indeterminate = selectedBookings.size > 0 &&
                selectedBookings.size < checkboxes.length;

            updateBulkActionsBar();
        }

        function updateBulkActionsBar() {
            const bar = document.getElementById('bulk-actions-bar');
            const count = document.getElementById('selected-count');
            const ids = Array.from(selectedBookings).join(',');

            count.textContent = selectedBookings.size;
            document.getElementById('bulk-export-ids').value = ids;
            document.querySelectorAll('.bulk-ids-input').forEach(input => {
                input.value = ids;
            });

            if (selectedBookings.size > 0) {
                bar.classList.remove('hidden');
            } else {
                bar.classList.add('hidden');
            }
        }

        function clearSelection() {
            selectedBookings.clear();
            document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('select-all').checked = false;
            document.getElementById('select-all').indeterminate = false;
            updateBulkActionsBar();
        }
    </script>
</x-app-layout>
