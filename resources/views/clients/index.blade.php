<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Clients</h1>
            <p class="text-slate-500">Database of all travelers across bookings</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('clients.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name or email..."
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
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
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
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
                            <a href="{{ route('clients.show', $traveler) }}" class="btn btn-secondary text-sm py-2 px-3">
                                View
                            </a>
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

        @if($travelers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $travelers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
