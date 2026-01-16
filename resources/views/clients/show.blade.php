<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('clients.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <span class="text-orange-600 font-bold text-lg">
                    {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                </span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $client->first_name }} {{ $client->last_name }}</h1>
                <p class="text-slate-500">
                    @if($client->is_lead)
                        Lead Traveler
                    @else
                        Traveler
                    @endif
                    @if($client->group && $client->group->booking)
                        | {{ $client->group->booking->country }}
                    @endif
                </p>
            </div>
        </div>
        <x-action-button type="edit" onclick="openEditClientModal()" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Contact Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                    <p class="text-slate-900">{{ $client->email ?: 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                    <p class="text-slate-900">{{ $client->phone ?: 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</label>
                    <p class="text-slate-900">{{ $client->dob ? $client->dob->format('F j, Y') : 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Booking Information -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Booking Information</h2>
            </div>
            <div class="p-6 space-y-4">
                @if($client->group && $client->group->booking)
                    @php $booking = $client->group->booking; @endphp
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Booking Number</label>
                        <p>
                            <a href="{{ route('bookings.show', $booking) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                                {{ $booking->booking_number }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Destination</label>
                        <p class="text-slate-900">{{ $booking->country }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Dates</label>
                        <p class="text-slate-900">{{ $booking->start_date->format('M j') }} - {{ $booking->end_date->format('M j, Y') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Status</label>
                        <p>
                            @if($booking->status === 'upcoming')
                                <span class="badge badge-info">Upcoming</span>
                            @elseif($booking->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge" style="background: #f1f5f9; color: #475569;">Completed</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Group</label>
                        <p class="text-slate-900">Group {{ $client->group->group_number }}</p>
                    </div>
                @else
                    <p class="text-slate-500">No booking associated</p>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Payment Information</h2>
            </div>
            <div class="p-6 space-y-4">
                @if($client->payment)
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Safari Rate</label>
                        <p class="text-slate-900 font-semibold">${{ number_format($client->payment->safari_rate, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Deposit (25%)</label>
                        <p class="text-slate-900">${{ number_format($client->payment->deposit, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">90-Day Payment (25%)</label>
                        <p class="text-slate-900">${{ number_format($client->payment->payment_90_day, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">45-Day Payment (50%)</label>
                        <p class="text-slate-900">${{ number_format($client->payment->payment_45_day, 2) }}</p>
                    </div>
                @else
                    <p class="text-slate-500">No payment information</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Communication History -->
    <div class="mt-6 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <livewire:client-notes :client="$client" />
    </div>

    <!-- Flight Information -->
    @if($client->flights->count() > 0)
        <div class="mt-6 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Flight Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($client->flights as $flight)
                        <div class="p-4 border border-slate-200 rounded-lg">
                            <div class="flex items-center gap-2 mb-3">
                                @if($flight->type === 'arrival')
                                    <span class="badge badge-success">Arrival</span>
                                @else
                                    <span class="badge badge-info">Departure</span>
                                @endif
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Airport</span>
                                    <span class="text-slate-900 font-medium">{{ $flight->airport }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Flight</span>
                                    <span class="text-slate-900 font-medium">{{ $flight->flight_number ?: '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Date</span>
                                    <span class="text-slate-900 font-medium">{{ $flight->date ? $flight->date->format('M j, Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Time</span>
                                    <span class="text-slate-900 font-medium">{{ $flight->time ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <x-edit-client-modal :client="$client" />

    <script>
        // Close modals when clicking outside
        document.querySelectorAll('[id$="-modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
