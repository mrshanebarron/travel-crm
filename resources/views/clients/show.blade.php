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
        <button type="button" onclick="openEditClientModal()" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            Edit
        </button>
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
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-slate-900">Communication History</h2>
            <button type="button" onclick="document.getElementById('add-note-modal').classList.remove('hidden')" class="btn btn-secondary text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Note
            </button>
        </div>
        <div class="p-6">
            @if($client->notes->count() > 0)
                <div class="space-y-4">
                    @foreach($client->notes->sortByDesc('contacted_at') as $note)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 {{ $note->getTypeColor() }} rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $note->getTypeIcon() }}" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 bg-slate-50 rounded-xl p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $note->getTypeColor() }}">
                                            {{ $note->getTypeLabel() }}
                                        </span>
                                        <span class="text-xs text-slate-500 ml-2">{{ $note->contacted_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                    <form method="POST" action="{{ route('client-notes.destroy', $note) }}" onsubmit="return confirm('Delete this note?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                    </form>
                                </div>
                                <div class="mt-2 text-slate-700 whitespace-pre-line">{{ $note->content }}</div>
                                <div class="mt-2 text-xs text-slate-400">
                                    Added by {{ $note->creator->name ?? 'Unknown' }} on {{ $note->created_at->format('M j, Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <p>No communication history</p>
                    <p class="text-sm">Click "Add Note" to start tracking interactions</p>
                </div>
            @endif
        </div>
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

    <!-- Add Note Modal -->
    <div id="add-note-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add Communication Note</h3>
            <form method="POST" action="{{ route('client-notes.store', $client) }}">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Type *</label>
                            <select name="type" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                @foreach(\App\Models\ClientNote::TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Contact Date/Time</label>
                            <input type="datetime-local" name="contacted_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                                class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes *</label>
                        <textarea name="content" rows="4" required
                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Describe the communication or interaction..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('add-note-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Note</button>
                </div>
            </form>
        </div>
    </div>

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
