<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Clients</h1>
        <p class="text-slate-500 text-sm sm:text-base">Database of all travelers across bookings</p>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name or email..."
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary flex-1 sm:flex-none justify-center">Search</button>
                @if(request('search'))
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary flex-1 sm:flex-none justify-center">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Clients -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($travelers as $traveler)
                <a href="{{ route('clients.show', $traveler) }}" class="block p-4 hover:bg-orange-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-600 font-semibold text-sm">
                                {{ strtoupper(substr($traveler->first_name, 0, 1) . substr($traveler->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">
                                        {{ $traveler->last_name }}, {{ $traveler->first_name }}
                                        @if($traveler->is_lead)
                                            <span class="text-xs text-orange-600">(Lead)</span>
                                        @endif
                                    </p>
                                    @if($traveler->email)
                                        <p class="text-sm text-slate-500 truncate">{{ $traveler->email }}</p>
                                    @endif
                                </div>
                                @if($traveler->group && $traveler->group->booking)
                                    @if($traveler->group->booking->status === 'upcoming')
                                        <span class="badge badge-info text-xs flex-shrink-0">Upcoming</span>
                                    @elseif($traveler->group->booking->status === 'active')
                                        <span class="badge badge-success text-xs flex-shrink-0">Active</span>
                                    @else
                                        <span class="badge text-xs flex-shrink-0" style="background: #f1f5f9; color: #475569;">Completed</span>
                                    @endif
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-slate-500">
                                @if($traveler->phone)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $traveler->phone }}
                                    </span>
                                @endif
                                @if($traveler->group && $traveler->group->booking)
                                    <span class="text-orange-600 font-medium">
                                        {{ $traveler->group->booking->booking_number }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-500">No clients found</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Booking</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($travelers as $traveler)
                        <tr class="hover:bg-slate-50">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-orange-600 font-semibold text-sm">
                                            {{ strtoupper(substr($traveler->first_name, 0, 1) . substr($traveler->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900">
                                            {{ $traveler->last_name }}, {{ $traveler->first_name }}
                                            @if($traveler->is_lead)
                                                <span class="text-xs text-orange-600 ml-1">(Lead)</span>
                                            @endif
                                        </div>
                                        @if($traveler->dob)
                                            <div class="text-xs text-slate-500">DOB: {{ $traveler->dob->format('M j, Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-slate-600">{{ $traveler->email ?: '-' }}</td>
                            <td class="text-slate-600">{{ $traveler->phone ?: '-' }}</td>
                            <td>
                                @if($traveler->group && $traveler->group->booking)
                                    <a href="{{ route('bookings.show', $traveler->group->booking) }}"
                                       class="text-orange-600 hover:text-orange-800 font-medium">
                                        {{ $traveler->group->booking->booking_number }}
                                    </a>
                                    <div class="text-xs text-slate-500">
                                        {{ $traveler->group->booking->start_date->format('M j') }} - {{ $traveler->group->booking->end_date->format('M j, Y') }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td>
                                @if($traveler->group && $traveler->group->booking)
                                    @if($traveler->group->booking->status === 'upcoming')
                                        <span class="badge badge-info">Upcoming</span>
                                    @elseif($traveler->group->booking->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge" style="background: #f1f5f9; color: #475569;">Completed</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <x-action-button type="view" size="xs" :href="route('clients.show', $traveler)" />
                                    <x-action-button type="edit" size="xs" onclick="openEditClientModal({{ $traveler->id }}, '{{ addslashes($traveler->first_name) }}', '{{ addslashes($traveler->last_name) }}', '{{ $traveler->email }}', '{{ $traveler->phone }}', '{{ $traveler->dob?->format('Y-m-d') }}', {{ $traveler->is_lead ? 'true' : 'false' }})" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                No clients found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($travelers->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-slate-200">
                {{ $travelers->links() }}
            </div>
        @endif
    </div>

    <x-edit-client-modal />
</x-app-layout>
