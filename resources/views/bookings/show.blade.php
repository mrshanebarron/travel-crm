<x-app-layout>
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('bookings.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $booking->booking_number }}</h1>
                <p class="text-slate-500">{{ $booking->country }} | {{ $booking->start_date->format('M j') }} - {{ $booking->end_date->format('M j, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($booking->status === 'upcoming')
                <span class="badge badge-info">Upcoming</span>
            @elseif($booking->status === 'active')
                <span class="badge badge-success">Active</span>
            @else
                <span class="badge" style="background: #f1f5f9; color: #475569;">Completed</span>
            @endif
            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-t-xl border border-slate-200 border-b-0">
        <div class="flex overflow-x-auto" id="booking-tabs">
            <button class="tab active flex items-center gap-2" data-tab="client-details">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Client Details</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="safari-plan">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span>Safari Plan</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="payment-details">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Payment Details</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="master-checklist">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span>Master Checklist</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="arrival-departure">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span>Arrival/Departure</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="documents">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Documents</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="ledger">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Ledger</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="rooms">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Rooms</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="activity-log">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Activity Log</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="email-history">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Email History</span>
            </button>
        </div>
    </div>

    <!-- Tab Content Container -->
    <div class="bg-white rounded-b-xl border border-slate-200 border-t-0">
        <!-- Client Details Tab -->
        <div class="tab-content active p-6" id="client-details">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Groups & Travelers</h2>
                <button type="button" onclick="document.getElementById('add-group-modal').classList.remove('hidden')" class="btn btn-primary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Group
                </button>
            </div>

            @forelse($booking->groups as $group)
                <div class="border border-slate-200 rounded-xl mb-6 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h3 class="font-semibold text-slate-900">Group {{ $group->group_number }}</h3>
                                <p class="text-sm text-slate-500">{{ $group->travelers->count() }} traveler(s)</p>
                            </div>
                            <button type="button" onclick="openAddTravelerModal({{ $group->id }})" class="btn btn-secondary text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Add Traveler
                            </button>
                        </div>
                        @if($group->rooms->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($group->rooms as $room)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        {{ $room->display_type }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400">No rooms assigned to this group</p>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-200">
                        @forelse($group->travelers as $traveler)
                            <div class="p-4 {{ $traveler->is_lead ? 'bg-orange-50' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                            <span class="text-orange-600 font-bold">
                                                {{ strtoupper(substr($traveler->first_name, 0, 1) . substr($traveler->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900">
                                                {{ $traveler->first_name }} {{ $traveler->last_name }}
                                                @if($traveler->is_lead)
                                                    <span class="badge badge-orange ml-2">Lead Traveler</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-slate-500 space-y-1 mt-1">
                                                @if($traveler->email)
                                                    <div>{{ $traveler->email }}</div>
                                                @endif
                                                @if($traveler->phone)
                                                    <div>{{ $traveler->phone }}</div>
                                                @endif
                                                @if($traveler->dob)
                                                    <div>DOB: {{ $traveler->dob->format('M j, Y') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($traveler->email)
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="btn btn-secondary text-sm py-1 px-2 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    Email
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-10">
                                                    <form method="POST" action="{{ route('emails.confirmation', [$booking, $traveler]) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Booking Confirmation</button>
                                                    </form>
                                                    <button type="button" onclick="openPaymentReminderModal({{ $booking->id }}, {{ $traveler->id }}, '{{ $traveler->full_name }}', {{ $traveler->total_cost ?? 0 }})" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Payment Reminder</button>
                                                    <form method="POST" action="{{ route('emails.document-request', [$booking, $traveler]) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Document Request</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('emails.itinerary', [$booking, $traveler]) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Itinerary Summary</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                        <a href="{{ route('clients.show', $traveler) }}" class="btn btn-secondary text-sm py-1 px-2">View</a>
                                        <form method="POST" action="{{ route('travelers.destroy', $traveler) }}" onsubmit="return confirm('Remove this traveler?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-500">
                                No travelers in this group yet
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p>No groups created yet</p>
                    <p class="text-sm">Click "Add Group" to get started</p>
                </div>
            @endforelse
        </div>

        <!-- Safari Plan Tab -->
        <div class="tab-content p-6" id="safari-plan">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Safari Itinerary</h2>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-secondary text-sm" onclick="document.getElementById('import-pdf-modal').classList.remove('hidden')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import PDF
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($booking->safariDays->sortBy('day_number') as $day)
                    <div class="border border-slate-200 rounded-xl p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center justify-center w-10 h-10 bg-orange-100 rounded-full text-orange-600 font-bold">
                                        {{ $day->day_number }}
                                    </span>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $day->date->format('l, F j, Y') }}</div>
                                        <div class="text-sm text-slate-600">{{ $day->location ?: 'Location TBD' }}</div>
                                    </div>
                                </div>
                                @if($day->lodge)
                                    <div class="ml-13 mt-2">
                                        <span class="text-sm text-slate-500">Accommodation:</span>
                                        <span class="text-sm font-medium text-slate-900">{{ $day->lodge }}</span>
                                    </div>
                                @endif
                                @if($day->morning_activity || $day->midday_activity || $day->afternoon_activity || $day->other_activities)
                                    <div class="ml-13 mt-2 text-sm space-y-1">
                                        @if($day->morning_activity)
                                            <div class="text-slate-600"><span class="text-slate-500">Morning:</span> {{ $day->morning_activity }}</div>
                                        @endif
                                        @if($day->midday_activity)
                                            <div class="text-slate-600"><span class="text-slate-500">Midday:</span> {{ $day->midday_activity }}</div>
                                        @endif
                                        @if($day->afternoon_activity)
                                            <div class="text-slate-600"><span class="text-slate-500">Afternoon:</span> {{ $day->afternoon_activity }}</div>
                                        @endif
                                        @if($day->other_activities)
                                            <div class="text-slate-600"><span class="text-slate-500">Other:</span> {{ $day->other_activities }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if($day->meal_plan)
                                    <span class="badge badge-info">{{ $day->meal_plan }}</span>
                                @endif
                                @if($day->drink_plan)
                                    <span class="badge badge-success">{{ $day->drink_plan }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <p>No safari itinerary added yet</p>
                        <p class="text-sm">Import a Safari Office PDF to populate the itinerary</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Payment Details Tab -->
        <div class="tab-content p-6" id="payment-details">
            <h2 class="text-lg font-semibold text-slate-900 mb-6">Payment Schedule</h2>

            @foreach($booking->groups as $group)
                <div class="border border-slate-200 rounded-xl mb-6 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                        <h3 class="font-semibold text-slate-900">Group {{ $group->group_number }} - Payment Summary</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Traveler</th>
                                    <th class="text-right">Safari Rate</th>
                                    <th class="text-right">Deposit (25%)</th>
                                    <th class="text-right">90-Day (25%)</th>
                                    <th class="text-right">45-Day (50%)</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $groupTotal = 0;
                                    $groupDeposit = 0;
                                    $group90Day = 0;
                                    $group45Day = 0;
                                @endphp
                                @foreach($group->travelers as $traveler)
                                    @php
                                        $payment = $traveler->payment;
                                        $safariRate = $payment ? $payment->safari_rate : 0;
                                        $deposit = $safariRate * 0.25;
                                        $payment90 = $safariRate * 0.25;
                                        $payment45 = $safariRate * 0.50;
                                        $groupTotal += $safariRate;
                                        $groupDeposit += $deposit;
                                        $group90Day += $payment90;
                                        $group45Day += $payment45;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="font-medium text-slate-900">
                                                {{ $traveler->first_name }} {{ $traveler->last_name }}
                                                @if($traveler->is_lead)
                                                    <span class="text-xs text-orange-600">(Lead)</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('payments.update', $payment ?? 'new') }}" class="inline" id="payment-form-{{ $traveler->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="safari_rate" value="{{ $safariRate }}" step="0.01" min="0"
                                                    class="w-28 text-right rounded border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500"
                                                    onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="text-right text-slate-600">${{ number_format($deposit, 2) }}</td>
                                        <td class="text-right text-slate-600">${{ number_format($payment90, 2) }}</td>
                                        <td class="text-right text-slate-600">${{ number_format($payment45, 2) }}</td>
                                        <td class="text-center">
                                            @if(!$payment)
                                                <form method="POST" action="{{ route('payments.store', $traveler) }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="safari_rate" value="0">
                                                    <button type="submit" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Initialize</button>
                                                </form>
                                            @else
                                                <span class="text-green-600 text-sm">Active</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50">
                                <tr>
                                    <td class="font-semibold text-slate-900">Group Total</td>
                                    <td class="text-right font-semibold text-slate-900">${{ number_format($groupTotal, 2) }}</td>
                                    <td class="text-right font-semibold text-slate-900">${{ number_format($groupDeposit, 2) }}</td>
                                    <td class="text-right font-semibold text-slate-900">${{ number_format($group90Day, 2) }}</td>
                                    <td class="text-right font-semibold text-slate-900">${{ number_format($group45Day, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach

            <!-- Payment Due Dates -->
            <div class="bg-orange-50 rounded-xl p-6 mt-6">
                <h3 class="font-semibold text-slate-900 mb-4">Payment Due Dates</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-slate-500">Deposit (25%)</div>
                        <div class="font-semibold text-slate-900">Upon Booking</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-slate-500">Second Payment (25%)</div>
                        <div class="font-semibold text-slate-900">{{ $booking->start_date->subDays(90)->format('M j, Y') }}</div>
                        <div class="text-xs text-slate-500">90 days before departure</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-slate-500">Final Payment (50%)</div>
                        <div class="font-semibold text-slate-900">{{ $booking->start_date->subDays(45)->format('M j, Y') }}</div>
                        <div class="text-xs text-slate-500">45 days before departure</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Checklist Tab -->
        <div class="tab-content p-6" id="master-checklist">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Master Checklist</h2>
                <button type="button" onclick="document.getElementById('add-task-modal').classList.remove('hidden')" class="btn btn-primary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Task
                </button>
            </div>

            <div class="space-y-4">
                <!-- Pending Tasks -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 px-6 py-3 border-b border-slate-200">
                        <h3 class="font-medium text-slate-900">Pending Tasks ({{ $booking->tasks->where('status', '!=', 'completed')->count() }})</h3>
                    </div>
                    <div class="divide-y divide-slate-200">
                        @forelse($booking->tasks->where('status', '!=', 'completed')->sortBy('due_date') as $task)
                            <div class="p-4 hover:bg-slate-50">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex items-start gap-3">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="mt-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="name" value="{{ $task->name }}">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="w-5 h-5 border-2 border-slate-300 rounded hover:border-orange-500 hover:bg-orange-50 transition-colors" title="Mark complete"></button>
                                        </form>
                                        <div class="flex-1">
                                            <div class="font-medium text-slate-900">{{ $task->name }}</div>
                                            <div class="flex flex-wrap gap-3 mt-1 text-sm">
                                                @if($task->due_date)
                                                    <span class="{{ $task->due_date->isPast() ? 'text-red-600 font-medium' : 'text-slate-500' }}">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $task->due_date->format('M j, Y') }}
                                                        @if($task->due_date->isPast()) (overdue) @endif
                                                    </span>
                                                @endif
                                                @if($task->assignedTo)
                                                    <span class="text-slate-500">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        {{ $task->assignedTo->name }}
                                                    </span>
                                                @else
                                                    <span class="text-amber-600">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        Unassigned
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="openEditTaskModal({{ $task->id }}, '{{ addslashes($task->name) }}', '{{ $task->due_date?->format('Y-m-d') }}', {{ $task->assigned_to ?? 'null' }})" class="text-slate-400 hover:text-slate-600" title="Edit task">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600" title="Delete task">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-500">
                                No pending tasks
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Completed Tasks -->
                @if($booking->tasks->where('status', 'completed')->count() > 0)
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="bg-green-50 px-6 py-3 border-b border-slate-200">
                            <h3 class="font-medium text-green-800">Completed Tasks ({{ $booking->tasks->where('status', 'completed')->count() }})</h3>
                        </div>
                        <div class="divide-y divide-slate-200">
                            @foreach($booking->tasks->where('status', 'completed')->sortByDesc('completed_at') as $task)
                                <div class="p-4 flex justify-between items-center bg-slate-50">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <div>
                                            <span class="text-slate-500 line-through">{{ $task->name }}</span>
                                            @if($task->completed_at)
                                                <span class="text-xs text-slate-400 ml-2">{{ $task->completed_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Arrival/Departure Tab -->
        <div class="tab-content p-6" id="arrival-departure">
            <h2 class="text-lg font-semibold text-slate-900 mb-6">Flight Details by Group</h2>

            @foreach($booking->groups as $group)
                <div class="border border-slate-200 rounded-xl mb-6 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-slate-900">Group {{ $group->group_number }}</h3>
                                <p class="text-sm text-slate-500">{{ $group->travelers->count() }} traveler(s)</p>
                            </div>
                        </div>
                    </div>

                    @foreach($group->travelers as $traveler)
                        <div class="border-b border-slate-100 last:border-b-0">
                            <div class="px-6 py-4 bg-white">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                            <span class="text-orange-600 font-bold text-sm">
                                                {{ strtoupper(substr($traveler->first_name, 0, 1) . substr($traveler->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-slate-900">{{ $traveler->first_name }} {{ $traveler->last_name }}</span>
                                            @if($traveler->is_lead)
                                                <span class="ml-2 badge badge-orange text-xs">Lead</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" onclick="openAddFlightModal({{ $traveler->id }})" class="btn btn-secondary text-sm py-1 px-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Flight
                                    </button>
                                </div>

                                @if($traveler->flights->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($traveler->flights as $flight)
                                            <div class="p-4 border border-slate-200 rounded-lg {{ $flight->type === 'arrival' ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-blue-500' }}">
                                                <div class="flex justify-between items-start mb-3">
                                                    @if($flight->type === 'arrival')
                                                        <span class="badge badge-success">Arrival</span>
                                                    @else
                                                        <span class="badge badge-info">Departure</span>
                                                    @endif
                                                    <form method="POST" action="{{ route('flights.destroy', $flight) }}" onsubmit="return confirm('Delete this flight?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Remove</button>
                                                    </form>
                                                </div>
                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-slate-500">Airport</span>
                                                        <span class="text-slate-900 font-medium">{{ $flight->airport }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-slate-500">Flight #</span>
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
                                                @if($flight->pickup_instructions || $flight->dropoff_instructions)
                                                    <div class="mt-3 pt-3 border-t border-slate-200 text-xs">
                                                        @if($flight->type === 'arrival' && $flight->pickup_instructions)
                                                            <div class="text-slate-600">
                                                                <span class="font-medium text-slate-500">Pickup:</span> {{ $flight->pickup_instructions }}
                                                            </div>
                                                        @endif
                                                        @if($flight->type === 'departure' && $flight->dropoff_instructions)
                                                            <div class="text-slate-600">
                                                                <span class="font-medium text-slate-500">Dropoff:</span> {{ $flight->dropoff_instructions }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-slate-400 italic">No flight details added</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Documents Tab -->
        <div class="tab-content p-6" id="documents">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Documents</h2>
            </div>

            <!-- Upload Form -->
            <form method="POST" action="{{ route('documents.store', $booking) }}" enctype="multipart/form-data" class="mb-6 p-4 bg-slate-50 rounded-xl">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Document Name</label>
                        <input type="text" name="name" placeholder="Document name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Category</label>
                        <select name="category" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="lodge">Lodges/Camps</option>
                            <option value="arrival_departure_flight">Arrival/Departure Flight</option>
                            <option value="internal_flight">Internal Flights</option>
                            <option value="passport">Passport</option>
                            <option value="safari_guide_invoice">Safari Guide Invoices</option>
                            <option value="misc">Miscellaneous</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">File</label>
                        <input type="file" name="file" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-full">Upload Document</button>
                    </div>
                </div>
            </form>

            <!-- Documents Grid -->
            @if($booking->documents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($booking->documents as $doc)
                        <div class="border border-slate-200 rounded-xl p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <a href="{{ route('documents.download', $doc) }}" class="font-medium text-orange-600 hover:text-orange-800">
                                            {{ $doc->name }}
                                        </a>
                                        @php
                                            $categoryLabels = [
                                                'lodge' => 'Lodges/Camps',
                                                'arrival_departure_flight' => 'Arrival/Departure Flight',
                                                'internal_flight' => 'Internal Flights',
                                                'passport' => 'Passport',
                                                'safari_guide_invoice' => 'Safari Guide Invoices',
                                                'misc' => 'Miscellaneous',
                                            ];
                                        @endphp
                                        <div class="text-xs text-slate-500">{{ $categoryLabels[$doc->category] ?? ucfirst($doc->category) }}</div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>No documents uploaded yet</p>
                </div>
            @endif
        </div>

        <!-- Ledger Tab -->
        <div class="tab-content p-6" id="ledger">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Financial Ledger</h2>
            </div>

            <!-- Add Entry Form -->
            <form method="POST" action="{{ route('ledger-entries.store', $booking) }}" class="mb-6 p-4 bg-slate-50 rounded-xl" id="ledger-entry-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Type</label>
                        <select name="type" id="ledger-type" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required onchange="toggleLedgerFields()">
                            <option value="received">Received</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div id="received-category-field">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payment Type</label>
                        <select name="received_category" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="deposit">Deposit (25%)</option>
                            <option value="90_day">90-Day Payment (25%)</option>
                            <option value="45_day">45-Day Payment (50%)</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div id="paid-category-field" class="hidden">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Expense Category</label>
                        <select name="paid_category" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="lodge">Lodge/Camp</option>
                            <option value="transport">Transport</option>
                            <option value="flights">Internal Flights</option>
                            <option value="park_fees">Park Fees</option>
                            <option value="guide">Safari Guide</option>
                            <option value="meals">Meals</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div id="vendor-field" class="hidden">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Vendor Name</label>
                        <input type="text" name="vendor_name" placeholder="Vendor name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Amount</label>
                        <input type="number" name="amount" placeholder="0.00" step="0.01" min="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes</label>
                        <input type="text" name="description" placeholder="Optional notes" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-full">Add Entry</button>
                    </div>
                </div>
            </form>

            <!-- Ledger Summary -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <div class="text-sm text-green-600 font-medium">Total Received</div>
                    <div class="text-2xl font-bold text-green-700">${{ number_format($booking->ledgerEntries->where('type', 'received')->sum('amount'), 2) }}</div>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <div class="text-sm text-red-600 font-medium">Total Paid</div>
                    <div class="text-2xl font-bold text-red-700">${{ number_format($booking->ledgerEntries->where('type', 'paid')->sum('amount'), 2) }}</div>
                </div>
                @php
                    $balance = $booking->ledgerEntries->where('type', 'received')->sum('amount') - $booking->ledgerEntries->where('type', 'paid')->sum('amount');
                @endphp
                <div class="{{ $balance >= 0 ? 'bg-purple-50' : 'bg-red-50' }} rounded-xl p-4 text-center">
                    <div class="text-sm {{ $balance >= 0 ? 'text-purple-600' : 'text-red-600' }} font-medium">Net Balance</div>
                    <div class="text-2xl font-bold {{ $balance >= 0 ? 'text-purple-700' : 'text-red-700' }}">${{ number_format($balance, 2) }}</div>
                </div>
            </div>

            <!-- Ledger Table -->
            @if($booking->ledgerEntries->count() > 0)
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-right">Received</th>
                                <th class="text-right">Paid</th>
                                <th class="text-right">Balance</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->ledgerEntries as $entry)
                                <tr>
                                    <td class="text-slate-900">{{ $entry->date->format('M j, Y') }}</td>
                                    <td class="text-slate-900">{{ $entry->description }}</td>
                                    <td class="text-right text-green-600 font-medium">
                                        {{ $entry->type === 'received' ? '$' . number_format($entry->amount, 2) : '' }}
                                    </td>
                                    <td class="text-right text-red-600 font-medium">
                                        {{ $entry->type === 'paid' ? '$' . number_format($entry->amount, 2) : '' }}
                                    </td>
                                    <td class="text-right font-semibold {{ $entry->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($entry->balance, 2) }}
                                    </td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('ledger-entries.destroy', $entry) }}" class="inline" onsubmit="return confirm('Delete this entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <p>No ledger entries yet</p>
                </div>
            @endif
        </div>

        <!-- Rooms Tab -->
        <div class="tab-content p-6" id="rooms">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Room Configuration</h2>
            </div>

            <!-- Add Room Form -->
            <form method="POST" action="{{ route('rooms.store', $booking) }}" class="mb-6 p-4 bg-slate-50 rounded-xl">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-end">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Room Type</label>
                        <select name="type" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="double">Double</option>
                            <option value="single">Single</option>
                            <option value="triple">Triple</option>
                            <option value="family">Family</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Assign to Group</label>
                        <select name="group_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">-- Unassigned --</option>
                            @foreach($booking->groups as $grp)
                                <option value="{{ $grp->id }}">Group {{ $grp->group_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Adults (18+)</label>
                        <input type="number" name="adults" placeholder="2" min="0" max="6" value="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Children 12-17</label>
                        <input type="number" name="children_12_17" placeholder="0" min="0" max="6" value="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Children 2-11</label>
                        <input type="number" name="children_2_11" placeholder="0" min="0" max="6" value="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Under 2</label>
                        <input type="number" name="children_under_2" placeholder="0" min="0" max="6" value="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-full">Add Room</button>
                    </div>
                </div>
            </form>

            <!-- Rooms Grid -->
            @if($booking->rooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($booking->rooms as $room)
                        <div class="border border-slate-200 rounded-xl p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900">{{ $room->display_type }}</div>
                                        <div class="text-sm text-slate-500">{{ $room->total_occupants }} occupant(s)</div>
                                        @if($room->group)
                                            <span class="inline-block mt-1 px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-xs font-medium">
                                                Group {{ $room->group->group_number }}
                                            </span>
                                        @else
                                            <span class="inline-block mt-1 px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-xs">
                                                Unassigned
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('rooms.destroy', $room) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Remove</button>
                                </form>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Adults (18+):</span>
                                    <span class="text-slate-700 font-medium">{{ $room->adults }}</span>
                                </div>
                                @if($room->children_12_17 > 0)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Children 12-17:</span>
                                        <span class="text-slate-700 font-medium">{{ $room->children_12_17 }}</span>
                                    </div>
                                @endif
                                @if($room->children_2_11 > 0)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Children 2-11:</span>
                                        <span class="text-slate-700 font-medium">{{ $room->children_2_11 }}</span>
                                    </div>
                                @endif
                                @if($room->children_under_2 > 0)
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Under 2:</span>
                                        <span class="text-slate-700 font-medium">{{ $room->children_under_2 }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <p>No rooms configured yet</p>
                </div>
            @endif
        </div>

        <!-- Activity Log Tab -->
        <div class="tab-content p-6" id="activity-log">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Activity Log</h2>
            </div>

            <!-- Add Note Form -->
            <form method="POST" action="{{ route('activity-logs.store', $booking) }}" class="mb-6 p-4 bg-slate-50 rounded-xl">
                @csrf
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Add Note</label>
                        <textarea name="notes" rows="2" placeholder="Add a note..." class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Add Note</button>
                    </div>
                </div>
            </form>

            <!-- Activity Timeline -->
            <div class="space-y-4">
                @forelse($booking->activityLogs->sortByDesc('created_at') as $log)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            @if($log->action_type === 'manual')
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-orange-600 font-semibold text-sm">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </span>
                                </div>
                            @else
                                <div class="w-10 h-10 {{ $log->action_color }} rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $log->action_icon }}" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 bg-white border border-slate-200 rounded-xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-slate-900">
                                        {{ $log->user->name ?? 'System' }}
                                        @if($log->action_type !== 'manual')
                                            <span class="ml-2 text-xs px-2 py-0.5 bg-slate-100 text-slate-600 rounded">Auto</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500">{{ $log->created_at->format('M j, Y g:i A') }} ({{ $log->created_at->diffForHumans() }})</div>
                                </div>
                                @if($log->action_type === 'manual')
                                    <form method="POST" action="{{ route('activity-logs.destroy', $log) }}" onsubmit="return confirm('Delete this note?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                    </form>
                                @endif
                            </div>
                            <div class="mt-2 text-slate-700">{{ $log->notes }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>No activity yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Email History Tab -->
        <div class="tab-content p-6" id="email-history">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Email Communications</h2>
            </div>

            <!-- Email History Table -->
            <div class="space-y-4">
                @forelse($booking->emailLogs->sortByDesc('sent_at') as $email)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 bg-white border border-slate-200 rounded-xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-slate-900">
                                        {{ $email->getTypeLabel() }}
                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        To: {{ $email->recipient_name ?? 'Unknown' }} &lt;{{ $email->recipient_email }}&gt;
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        Subject: {{ $email->subject }}
                                    </div>
                                    @if($email->notes)
                                        <div class="text-xs text-slate-400 mt-1">{{ $email->notes }}</div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-slate-500">{{ $email->sent_at->format('M j, Y g:i A') }}</div>
                                    <div class="text-xs text-slate-400">{{ $email->sent_at->diffForHumans() }}</div>
                                    @if($email->sender)
                                        <div class="text-xs text-slate-400 mt-1">by {{ $email->sender->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p>No emails sent yet</p>
                        <p class="text-sm">Send emails to travelers using the Email button on their profile</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Add Traveler Modal -->
    <div id="add-traveler-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add Traveler</h3>
            <form id="add-traveler-form" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">First Name</label>
                            <input type="text" name="first_name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Last Name</label>
                            <input type="text" name="last_name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                        <input type="text" name="phone" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</label>
                        <input type="date" name="dob" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_lead" id="is_lead" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        <label for="is_lead" class="text-sm text-slate-700">Lead Traveler</label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('add-traveler-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Traveler</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Flight Modal -->
    <div id="add-flight-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add Flight</h3>
            <form id="add-flight-form" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Type</label>
                            <select name="type" id="flight-type-select" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required onchange="toggleFlightInstructions()">
                                <option value="arrival">Arrival</option>
                                <option value="departure">Departure</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Airport</label>
                            <input type="text" name="airport" placeholder="e.g., JRO" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Flight Number</label>
                            <input type="text" name="flight_number" placeholder="e.g., KQ 100" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date</label>
                            <input type="date" name="date" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Time</label>
                        <input type="time" name="time" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div id="pickup-instructions-field">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Pickup Instructions</label>
                        <textarea name="pickup_instructions" rows="2" placeholder="Where to pick up the traveler, vehicle details, driver contact, etc." class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                    </div>
                    <div id="dropoff-instructions-field" class="hidden">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Dropoff Instructions</label>
                        <textarea name="dropoff_instructions" rows="2" placeholder="Where to drop off the traveler, special instructions, etc." class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('add-flight-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Flight</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Group Modal -->
    <div id="add-group-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add New Group</h3>
            <form method="POST" action="{{ route('groups.store', $booking) }}">
                @csrf
                <div class="space-y-4">
                    <p class="text-sm text-slate-600">
                        This will create a new group for this booking. Groups allow you to organize travelers who are billed together separately from other travelers.
                    </p>
                    <p class="text-sm text-slate-500">
                        The new group will be numbered Group {{ ($booking->groups->max('group_number') ?? 0) + 1 }}.
                    </p>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('add-group-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import PDF Modal -->
    <div id="import-pdf-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Import Safari Office PDF</h3>
            <form method="POST" action="{{ route('bookings.import-pdf', $booking) }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Safari Office PDF</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" required>
                    </div>
                    <p class="text-sm text-slate-500">Upload a Safari Office booking confirmation PDF to automatically populate the itinerary.</p>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('import-pdf-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import PDF</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="add-task-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add New Task</h3>
            <form method="POST" action="{{ route('tasks.store', $booking) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Task Name</label>
                        <input type="text" name="name" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500" placeholder="Enter task name..." required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Due Date</label>
                        <input type="date" name="due_date" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Assign To</label>
                        <select name="assigned_to" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                            <option value="">-- Unassigned --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('add-task-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="edit-task-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit Task</h3>
            <form id="edit-task-form" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Task Name</label>
                        <input type="text" name="name" id="edit-task-name" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Due Date</label>
                        <input type="date" name="due_date" id="edit-task-due-date" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Assign To</label>
                        <select name="assigned_to" id="edit-task-assigned-to" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                            <option value="">-- Unassigned --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Status</label>
                        <select name="status" id="edit-task-status" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('edit-task-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Reminder Modal -->
    <div id="payment-reminder-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Send Payment Reminder</h3>
            <form id="payment-reminder-form" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Recipient</label>
                        <p id="payment-reminder-recipient" class="text-slate-900 font-medium"></p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payment Type *</label>
                        <select name="payment_type" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                            <option value="deposit">25% Deposit</option>
                            <option value="second">Second Payment (25%)</option>
                            <option value="final">Final Payment (50%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Amount Due *</label>
                        <input type="number" name="amount_due" id="payment-reminder-amount" step="0.01" required
                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Due Date *</label>
                        <input type="date" name="due_date" required
                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('payment-reminder-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Reminder</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('#booking-tabs .tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active from all tabs
                document.querySelectorAll('#booking-tabs .tab').forEach(t => t.classList.remove('active'));
                // Remove active from all content
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                // Add active to clicked tab
                this.classList.add('active');
                // Show corresponding content
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        // Add Traveler Modal
        function openAddTravelerModal(groupId) {
            const form = document.getElementById('add-traveler-form');
            form.action = `/groups/${groupId}/travelers`;
            document.getElementById('add-traveler-modal').classList.remove('hidden');
        }

        // Add Flight Modal
        function openAddFlightModal(travelerId) {
            const form = document.getElementById('add-flight-form');
            form.action = `/travelers/${travelerId}/flights`;
            document.getElementById('add-flight-modal').classList.remove('hidden');
        }

        // Payment Reminder Modal
        function openPaymentReminderModal(bookingId, travelerId, travelerName, totalCost) {
            const form = document.getElementById('payment-reminder-form');
            form.action = `/bookings/${bookingId}/travelers/${travelerId}/email/payment-reminder`;
            document.getElementById('payment-reminder-recipient').textContent = travelerName;
            document.getElementById('payment-reminder-amount').value = totalCost > 0 ? (totalCost * 0.25).toFixed(2) : '';
            document.getElementById('payment-reminder-modal').classList.remove('hidden');
        }

        // Edit Task Modal
        function openEditTaskModal(taskId, name, dueDate, assignedTo) {
            const form = document.getElementById('edit-task-form');
            form.action = `/tasks/${taskId}`;
            document.getElementById('edit-task-name').value = name;
            document.getElementById('edit-task-due-date').value = dueDate || '';
            document.getElementById('edit-task-assigned-to').value = assignedTo || '';
            document.getElementById('edit-task-status').value = 'pending';
            document.getElementById('edit-task-modal').classList.remove('hidden');
        }

        // Close modals when clicking outside
        document.querySelectorAll('[id$="-modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });

        // Toggle ledger form fields based on type
        function toggleLedgerFields() {
            const type = document.getElementById('ledger-type').value;
            const receivedField = document.getElementById('received-category-field');
            const paidField = document.getElementById('paid-category-field');
            const vendorField = document.getElementById('vendor-field');

            if (type === 'received') {
                receivedField.classList.remove('hidden');
                paidField.classList.add('hidden');
                vendorField.classList.add('hidden');
            } else {
                receivedField.classList.add('hidden');
                paidField.classList.remove('hidden');
                vendorField.classList.remove('hidden');
            }
        }

        // Toggle flight pickup/dropoff instructions based on type
        function toggleFlightInstructions() {
            const type = document.getElementById('flight-type-select').value;
            const pickupField = document.getElementById('pickup-instructions-field');
            const dropoffField = document.getElementById('dropoff-instructions-field');

            if (type === 'arrival') {
                pickupField.classList.remove('hidden');
                dropoffField.classList.add('hidden');
            } else {
                pickupField.classList.add('hidden');
                dropoffField.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
