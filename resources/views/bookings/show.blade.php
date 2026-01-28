<x-app-layout>
    @php
        $leadTraveler = $booking->groups->flatMap->travelers->firstWhere('is_lead', true);
    @endphp

    <!-- Persistent Booking Header - Shows on all tabs -->
    <div class="mb-4 bg-orange-50 border border-orange-200 rounded-xl px-4 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-lg font-bold text-orange-800">{{ $booking->booking_number }}</span>
                <span class="text-orange-600">|</span>
                <span class="text-orange-800 font-medium">
                    @if($leadTraveler)
                        {{ $leadTraveler->last_name }}, {{ $leadTraveler->first_name }}
                    @else
                        No Lead Traveler
                    @endif
                </span>
            </div>
            <span class="text-sm text-orange-600">{{ $booking->country }} | {{ $booking->start_date->format('M j') }} - {{ $booking->end_date->format('M j, Y') }}</span>
        </div>
    </div>

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
                @if(!empty($booking->guides))
                    <p class="text-sm text-slate-400 mt-1">
                        Guides:
                        @foreach($booking->guides as $country => $guide)
                            <span class="text-slate-600">{{ $country }}: {{ $guide }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </p>
                @endif
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
            <x-action-button type="edit" size="sm" :href="route('bookings.edit', $booking)" />
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
            @can('view_financial_data')
            <button class="tab flex items-center gap-2" data-tab="payment-details">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Rates & Payments</span>
            </button>
            @endcan
            <button class="tab flex items-center gap-2" data-tab="master-checklist">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span>Checklist</span>
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
            @can('view_financial_data')
            <button class="tab flex items-center gap-2" data-tab="ledger">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Ledger</span>
            </button>
            @endcan
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
                <span>Activity</span>
            </button>
            <button class="tab flex items-center gap-2" data-tab="email-history">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Emails</span>
            </button>
        </div>
    </div>

    <!-- Tab Content Container -->
    <div class="bg-white rounded-b-xl border border-slate-200 border-t-0">
        <!-- Client Details Tab -->
        <div class="tab-content active p-6" id="client-details">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Groups & Travelers</h2>
                <div class="flex items-center gap-2">
                    <!-- Intake Form Link -->
                    <div x-data="{ showCopied: false }" class="flex items-center gap-2">
                        @if($booking->intake_token)
                            <button
                                @click="navigator.clipboard.writeText('{{ route('intake.show', $booking->intake_token) }}'); showCopied = true; setTimeout(() => showCopied = false, 2000)"
                                class="inline-flex items-center gap-1 text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span x-text="showCopied ? 'Copied!' : 'Copy Intake Link'"></span>
                            </button>
                        @else
                            <form action="{{ route('bookings.generate-intake-token', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 text-xs px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    Generate Intake Link
                                </button>
                            </form>
                        @endif
                    </div>
                    <x-action-button type="add" size="sm" label="Add Group" onclick="document.getElementById('add-group-modal').classList.remove('hidden')" />
                </div>
            </div>

            @forelse($booking->groups as $group)
                <div class="border border-slate-200 rounded-xl mb-6 overflow-hidden">
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h3 class="font-semibold text-slate-900">Group {{ $group->group_number }}</h3>
                                <p class="text-sm text-slate-500">{{ $group->travelers->count() }} traveler(s)</p>
                            </div>
                            <x-action-button type="adduser" size="sm" onclick="openAddTravelerModal({{ $group->id }})" />
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
                                                <button @click="open = !open" class="inline-flex items-center font-medium rounded border transition-colors text-xs py-0.5 px-1.5 gap-1 bg-blue-600 border-blue-600 text-white hover:bg-blue-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    Email
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-10">
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
                                        <x-action-button type="view" size="xs" :href="route('clients.show', $traveler)" />
                                        <x-action-button type="edit" size="xs" onclick="openEditTravelerModal({{ $traveler->id }}, '{{ addslashes($traveler->first_name) }}', '{{ addslashes($traveler->last_name) }}', '{{ $traveler->email }}', '{{ $traveler->phone }}', '{{ $traveler->dob?->format('Y-m-d') }}', {{ $traveler->is_lead ? 'true' : 'false' }})" />
                                        <x-action-button type="delete" size="xs" :action="route('travelers.destroy', $traveler)" confirm="Remove this traveler?" />
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
                    <svg class="mx-auto mb-4 text-slate-300" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <x-action-button type="import" size="sm" label="Import from Safari Office" @click="$dispatch('open-import-modal')" />
                </div>
            </div>

            @if($booking->safariDays->count())
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider w-1/5">Day / Destination</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider w-1/5">Morning</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider w-1/5">Midday</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider w-1/5">Afternoon</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider w-1/5">Evening</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($booking->safariDays->sortBy('day_number') as $day)
                                @php
                                    $morningActivities = $day->activitiesForPeriod('morning');
                                    $middayActivities = $day->activitiesForPeriod('midday');
                                    $afternoonActivities = $day->activitiesForPeriod('afternoon');
                                    $eveningActivities = $day->activitiesForPeriod('evening');
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    {{-- Day Info Column --}}
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex items-start gap-3">
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 rounded-full text-orange-600 font-bold text-sm shrink-0">
                                                {{ $day->day_number }}
                                            </span>
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-slate-900">{{ $day->date->format('M j, Y') }}</div>
                                                <div class="text-sm text-slate-600 font-medium">{{ $day->location ?: 'TBD' }}</div>
                                                @if($day->lodge)
                                                    <div class="text-xs text-slate-500 mt-1">{{ $day->lodge }}</div>
                                                @endif
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @if($day->meal_plan)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $day->meal_plan }}</span>
                                                    @endif
                                                    @if($day->drink_plan)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">{{ $day->drink_plan }}</span>
                                                    @endif
                                                </div>
                                                @if(auth()->user()->isSuperAdmin())
                                                    <button onclick="openEditDayModal({{ $day->id }})" class="text-xs text-orange-600 hover:text-orange-800 mt-1">Edit</button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Morning Column --}}
                                    <td class="px-4 py-4 align-top">
                                        <div class="space-y-1" x-data="activityCell({{ $day->id }}, 'morning', {{ json_encode($morningActivities->pluck('activity')->toArray()) }})">
                                            <template x-if="!editing">
                                                <div>
                                                    <template x-for="(activity, index) in activities" :key="index">
                                                        <div class="text-sm text-slate-700" x-text="activity"></div>
                                                    </template>
                                                    <div x-show="activities.length === 0" class="text-sm text-slate-400">-</div>
                                                    @if(auth()->user()->isSuperAdmin())
                                                        <button @click="startEdit()" class="text-xs text-orange-600 hover:text-orange-800 mt-1">Edit</button>
                                                    @endif
                                                </div>
                                            </template>
                                            <template x-if="editing">
                                                <div>
                                                    <textarea x-model="editText" rows="3" class="w-full text-sm border-slate-300 rounded focus:border-orange-500 focus:ring-orange-500" placeholder="One activity per line"></textarea>
                                                    <div class="flex gap-1 mt-1">
                                                        <button @click="save()" class="text-xs text-green-600 hover:text-green-800">Save</button>
                                                        <button @click="cancel()" class="text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    {{-- Midday Column --}}
                                    <td class="px-4 py-4 align-top">
                                        @php
                                            $middayDefault = $middayActivities->isEmpty() ? ['Lunch'] : $middayActivities->pluck('activity')->toArray();
                                        @endphp
                                        <div class="space-y-1" x-data="activityCell({{ $day->id }}, 'midday', {{ json_encode($middayDefault) }})">
                                            <template x-if="!editing">
                                                <div>
                                                    <template x-for="(activity, index) in activities" :key="index">
                                                        <div class="text-sm text-slate-700" x-text="activity"></div>
                                                    </template>
                                                    @if(auth()->user()->isSuperAdmin())
                                                        <button @click="startEdit()" class="text-xs text-orange-600 hover:text-orange-800 mt-1">Edit</button>
                                                    @endif
                                                </div>
                                            </template>
                                            <template x-if="editing">
                                                <div>
                                                    <textarea x-model="editText" rows="3" class="w-full text-sm border-slate-300 rounded focus:border-orange-500 focus:ring-orange-500" placeholder="One activity per line"></textarea>
                                                    <div class="flex gap-1 mt-1">
                                                        <button @click="save()" class="text-xs text-green-600 hover:text-green-800">Save</button>
                                                        <button @click="cancel()" class="text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    {{-- Afternoon Column --}}
                                    <td class="px-4 py-4 align-top">
                                        <div class="space-y-1" x-data="activityCell({{ $day->id }}, 'afternoon', {{ json_encode($afternoonActivities->pluck('activity')->toArray()) }})">
                                            <template x-if="!editing">
                                                <div>
                                                    <template x-for="(activity, index) in activities" :key="index">
                                                        <div class="text-sm text-slate-700" x-text="activity"></div>
                                                    </template>
                                                    <div x-show="activities.length === 0" class="text-sm text-slate-400">-</div>
                                                    @if(auth()->user()->isSuperAdmin())
                                                        <button @click="startEdit()" class="text-xs text-orange-600 hover:text-orange-800 mt-1">Edit</button>
                                                    @endif
                                                </div>
                                            </template>
                                            <template x-if="editing">
                                                <div>
                                                    <textarea x-model="editText" rows="3" class="w-full text-sm border-slate-300 rounded focus:border-orange-500 focus:ring-orange-500" placeholder="One activity per line"></textarea>
                                                    <div class="flex gap-1 mt-1">
                                                        <button @click="save()" class="text-xs text-green-600 hover:text-green-800">Save</button>
                                                        <button @click="cancel()" class="text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    {{-- Evening Column --}}
                                    <td class="px-4 py-4 align-top">
                                        <div class="space-y-1" x-data="activityCell({{ $day->id }}, 'evening', {{ json_encode($eveningActivities->pluck('activity')->toArray()) }})">
                                            <template x-if="!editing">
                                                <div>
                                                    <template x-for="(activity, index) in activities" :key="index">
                                                        <div class="text-sm text-slate-700" x-text="activity"></div>
                                                    </template>
                                                    <div x-show="activities.length === 0" class="text-sm text-slate-400">-</div>
                                                    @if(auth()->user()->isSuperAdmin())
                                                        <button @click="startEdit()" class="text-xs text-orange-600 hover:text-orange-800 mt-1">Edit</button>
                                                    @endif
                                                </div>
                                            </template>
                                            <template x-if="editing">
                                                <div>
                                                    <textarea x-model="editText" rows="3" class="w-full text-sm border-slate-300 rounded focus:border-orange-500 focus:ring-orange-500" placeholder="One activity per line"></textarea>
                                                    <div class="flex gap-1 mt-1">
                                                        <button @click="save()" class="text-xs text-green-600 hover:text-green-800">Save</button>
                                                        <button @click="cancel()" class="text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-slate-500">
                    <svg class="mx-auto mb-4 text-slate-300" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <p>No safari itinerary added yet</p>
                    <p class="text-sm">Import a Safari Office PDF to populate the itinerary</p>
                </div>
            @endif
        </div>

        <!-- Payment Details Tab -->
        @can('view_financial_data')
        <div class="tab-content p-6" id="payment-details">
            {{-- Safari Rates & Payment Schedule - Livewire Component --}}
            <livewire:booking-payments :booking="$booking" />

            <!-- Add-ons Section -->
            <div class="border border-slate-200 rounded-xl mb-6 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-900">Add-ons & Experiences</h3>
                    <x-action-button type="add" size="sm" label="Add Experience" onclick="document.getElementById('add-addon-modal').classList.remove('hidden')" />
                </div>
                <div class="p-6">
                    @php
                        $allAddons = $booking->groups->flatMap->travelers->flatMap->addons;
                        $addonsOnly = $allAddons->where('type', '!=', 'credit');
                        $creditsOnly = $allAddons->where('type', 'credit');
                        $netTotal = $addonsOnly->sum('cost_per_person') - $creditsOnly->sum('cost_per_person');
                    @endphp
                    @if($allAddons->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Traveler</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th class="text-right">Amount</th>
                                        <th>Notes</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allAddons as $addon)
                                        <tr class="{{ $addon->type === 'credit' ? 'bg-blue-50' : '' }}">
                                            <td class="font-medium text-slate-900">{{ $addon->traveler->full_name }}</td>
                                            <td>
                                                @if($addon->type === 'credit')
                                                    <span class="badge badge-info">Credit</span>
                                                @else
                                                    <span class="badge badge-slate">Add-on</span>
                                                @endif
                                            </td>
                                            <td>{{ $addon->experience_name }}</td>
                                            <td class="text-right {{ $addon->type === 'credit' ? 'text-blue-600' : '' }}">
                                                {{ $addon->type === 'credit' ? '-' : '' }}${{ number_format($addon->cost_per_person, 2) }}
                                            </td>
                                            <td class="text-slate-600 text-sm">{{ $addon->notes ?: '-' }}</td>
                                            <td class="text-center">
                                                @if($addon->type === 'credit')
                                                    <span class="badge badge-success">Applied</span>
                                                @elseif($addon->paid)
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <form method="POST" action="{{ route('traveler-addons.mark-paid', $addon) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="badge badge-warning cursor-pointer hover:bg-yellow-200">Unpaid</button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form method="POST" action="{{ route('traveler-addons.destroy', $addon) }}" onsubmit="return confirm('Delete this {{ $addon->type === 'credit' ? 'credit' : 'add-on' }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-slate-50">
                                    <tr>
                                        <td colspan="3" class="font-semibold text-slate-900">Add-ons Subtotal</td>
                                        <td class="text-right font-semibold text-slate-900">${{ number_format($addonsOnly->sum('cost_per_person'), 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    @if($creditsOnly->count() > 0)
                                    <tr>
                                        <td colspan="3" class="font-semibold text-blue-600">Credits Subtotal</td>
                                        <td class="text-right font-semibold text-blue-600">-${{ number_format($creditsOnly->sum('cost_per_person'), 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    @endif
                                    <tr class="border-t-2 border-slate-300">
                                        <td colspan="3" class="font-bold text-slate-900">Net Add-ons Total</td>
                                        <td class="text-right font-bold text-slate-900">${{ number_format($netTotal, 2) }}</td>
                                        <td></td>
                                        <td class="text-center text-sm text-slate-600">
                                            {{ $addonsOnly->where('paid', true)->count() }}/{{ $addonsOnly->count() }} paid
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-500">
                            <p>No add-ons or credits added yet.</p>
                            <p class="text-sm">Click "Add Experience" to add extras (balloon rides, spa) or credits (refunds, discounts).</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Due Dates -->
            <div class="bg-orange-50 rounded-xl p-6 mt-6">
                <h3 class="font-semibold text-slate-900 mb-4">Payment Due Dates</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-slate-500">Deposit</div>
                        <div class="font-semibold text-slate-900">Upon Booking</div>
                        <div class="text-xs text-slate-500">Locked once entered</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-slate-500">Second Payment</div>
                        <div class="font-semibold text-slate-900">{{ $booking->start_date->subDays(90)->format('M j, Y') }}</div>
                        <div class="text-xs text-slate-500">90 days before departure</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-slate-500">Final Payment</div>
                        <div class="font-semibold text-slate-900">{{ $booking->start_date->subDays(45)->format('M j, Y') }}</div>
                        <div class="text-xs text-slate-500">45 days before departure</div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <!-- Master Checklist Tab -->
        <div class="tab-content p-6" id="master-checklist">
            <livewire:booking-task-list :booking="$booking" />
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
                                    <x-action-button type="flight" size="xs" onclick="openAddFlightModal({{ $traveler->id }})" />
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
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" onclick="openCopyFlightModal({{ $flight->id }}, '{{ $flight->type }}', '{{ addslashes($flight->airport) }}', '{{ $flight->flight_number }}', {{ $traveler->id }})" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Copy</button>
                                                        <form method="POST" action="{{ route('flights.destroy', $flight) }}" onsubmit="return confirm('Delete this flight?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Remove</button>
                                                        </form>
                                                    </div>
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
            <livewire:booking-documents :booking="$booking" />
        </div>

        <!-- Ledger Tab -->
        @can('view_financial_data')
        <div class="tab-content p-6" id="ledger">
            <livewire:booking-ledger :booking="$booking" />
        </div>
        @endcan

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
                        <x-action-button type="add" label="Add Room" :submit="true" class="w-full justify-center" />
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
                    <svg class="mx-auto mb-4 text-slate-300" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <p>No rooms configured yet</p>
                </div>
            @endif
        </div>

        <!-- Activity Log Tab -->
        <div class="tab-content p-6" id="activity-log">
            <livewire:booking-activity-log :booking="$booking" />
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
                        <svg class="mx-auto mb-4 text-slate-300" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <x-action-button type="cancel" onclick="document.getElementById('add-traveler-modal').classList.add('hidden')" />
                    <x-action-button type="adduser" :submit="true" />
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Traveler Modal -->
    <div id="edit-traveler-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit Traveler</h3>
            <form id="edit-traveler-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">First Name</label>
                            <input type="text" name="first_name" id="edit-traveler-first-name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Last Name</label>
                            <input type="text" name="last_name" id="edit-traveler-last-name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" id="edit-traveler-email" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                        <input type="text" name="phone" id="edit-traveler-phone" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</label>
                        <input type="date" name="dob" id="edit-traveler-dob" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_lead" id="edit-traveler-is-lead" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        <label for="edit-traveler-is-lead" class="text-sm text-slate-700">Lead Traveler</label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" onclick="document.getElementById('edit-traveler-modal').classList.add('hidden')" />
                    <x-action-button type="save" label="Save Changes" :submit="true" />
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
                    <!-- Copy from another traveler -->
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Copy from another traveler (optional)</label>
                        <select id="copy-from-traveler" class="w-full mt-1 rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" onchange="copyFlightFromTraveler(this.value)">
                            <option value="">-- Enter manually --</option>
                            @foreach($booking->groups as $group)
                                @foreach($group->travelers as $t)
                                    @foreach($t->flights as $f)
                                        <option value="{{ $f->id }}" data-type="{{ $f->type }}" data-airport="{{ $f->airport }}" data-flight-number="{{ $f->flight_number }}" data-date="{{ $f->date?->format('Y-m-d') }}" data-time="{{ $f->time }}">
                                            {{ $t->first_name }} {{ $t->last_name }} - {{ ucfirst($f->type) }} ({{ $f->airport }}{{ $f->flight_number ? ', ' . $f->flight_number : '' }})
                                        </option>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </select>
                    </div>

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
                            <input type="text" name="airport" id="flight-airport" placeholder="e.g., JRO" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Flight Number</label>
                            <input type="text" name="flight_number" id="flight-number" placeholder="e.g., KQ 100" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date</label>
                            <input type="date" name="date" id="flight-date" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Time</label>
                        <input type="time" name="time" id="flight-time" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
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
                    <x-action-button type="cancel" onclick="document.getElementById('add-flight-modal').classList.add('hidden')" />
                    <x-action-button type="flight" :submit="true" />
                </div>
            </form>
        </div>
    </div>

    <!-- Copy Flight Modal -->
    <div id="copy-flight-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Copy Flight to Other Travelers</h3>
            <p class="text-sm text-slate-500 mb-4" id="copy-flight-info"></p>
            <form id="copy-flight-form" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="text-sm font-medium text-slate-700 mb-2">Select travelers to copy this flight to:</div>
                    <div class="max-h-64 overflow-y-auto border border-slate-200 rounded-lg divide-y divide-slate-200">
                        @foreach($booking->groups as $group)
                            <div class="bg-slate-50 px-4 py-2 font-medium text-slate-700 text-sm sticky top-0">
                                Group {{ $group->group_number }}
                            </div>
                            @foreach($group->travelers as $t)
                                <label class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer copy-flight-traveler-option" data-traveler-id="{{ $t->id }}">
                                    <input type="checkbox" name="traveler_ids[]" value="{{ $t->id }}" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                    <span class="text-slate-900">{{ $t->first_name }} {{ $t->last_name }}</span>
                                    @if($t->is_lead)
                                        <span class="badge badge-orange text-xs">Lead</span>
                                    @endif
                                </label>
                            @endforeach
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="selectAllTravelers()" class="text-sm text-orange-600 hover:text-orange-800 font-medium">Select All</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" onclick="deselectAllTravelers()" class="text-sm text-slate-600 hover:text-slate-800 font-medium">Deselect All</button>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" onclick="document.getElementById('copy-flight-modal').classList.add('hidden')" />
                    <x-action-button type="save" label="Copy Flight" :submit="true" />
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
                    <x-action-button type="cancel" onclick="document.getElementById('add-group-modal').classList.add('hidden')" />
                    <x-action-button type="create" label="Create Group" :submit="true" />
                </div>
            </form>
        </div>
    </div>

    <!-- Import Safari Office Modal -->
    <div x-data="{ open: false, tab: 'url' }"
         x-show="open"
         x-cloak
         x-on:open-import-modal.window="open = true"
         x-on:keydown.escape.window="open = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-lg" @click.outside="open = false">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Import from Safari Office</h3>

            <!-- Tabs for import type -->
            <div class="flex gap-4 mb-6 border-b border-slate-200">
                <button type="button"
                        @click="tab = 'url'"
                        :class="tab === 'url' ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500'"
                        class="pb-2 px-1 border-b-2 font-medium text-sm">Online Booking URL</button>
                <button type="button"
                        @click="tab = 'pdf'"
                        :class="tab === 'pdf' ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500'"
                        class="pb-2 px-1 border-b-2 font-medium text-sm">PDF Upload</button>
            </div>

            <!-- URL Import Form -->
            <form x-show="tab === 'url'" method="POST" action="{{ route('bookings.import-url', $booking) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Safari Office Booking URL</label>
                        <input type="url" name="safari_office_url" class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500" placeholder="https://company.safarioffice.app/abc123/online" required>
                    </div>
                    <p class="text-sm text-slate-500">Paste the Safari Office online booking link. This will extract the itinerary directly from the webpage.</p>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" @click="open = false" />
                    <x-action-button type="import" label="Import from URL" :submit="true" />
                </div>
            </form>

            <!-- PDF Import Form -->
            <form x-show="tab === 'pdf'" method="POST" action="{{ route('bookings.import-pdf', $booking) }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Safari Office PDF</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" required>
                    </div>
                    <p class="text-sm text-slate-500">Upload a Safari Office booking confirmation PDF to automatically populate the itinerary.</p>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" @click="open = false" />
                    <x-action-button type="import" label="Import PDF" :submit="true" />
                </div>
            </form>
        </div>
    </div>

    <!-- Add Addon Modal -->
    <div id="add-addon-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add Experience / Credit</h3>
            <form id="add-addon-form" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Type</label>
                        <select name="type" id="addon-type-select" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" onchange="updateAddonLabels()">
                            <option value="add_on">Add-on (Charge)</option>
                            <option value="credit">Credit (Refund/Discount)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Traveler</label>
                        <select name="traveler_id" id="addon-traveler-select" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required onchange="updateAddonFormAction()">
                            <option value="">Select traveler...</option>
                            @foreach($booking->groups as $group)
                                @foreach($group->travelers as $traveler)
                                    <option value="{{ $traveler->id }}">{{ $traveler->full_name }} (Group {{ $group->group_number }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label id="addon-name-label" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Experience Name</label>
                        <input type="text" name="experience_name" id="addon-name-input" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" placeholder="e.g., Balloon Safari, Spa Treatment" required>
                    </div>
                    <div>
                        <label id="addon-cost-label" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Cost Per Person ($)</label>
                        <input type="number" name="cost_per_person" step="0.01" min="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes (optional)</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Any special details..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" onclick="document.getElementById('add-addon-modal').classList.add('hidden')" />
                    <x-action-button type="add" label="Add Experience" :submit="true" id="addon-submit-btn" />
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
                    <x-action-button type="cancel" onclick="document.getElementById('payment-reminder-modal').classList.add('hidden')" />
                    <x-action-button type="email" label="Send Reminder" :submit="true" />
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Day Modal -->
    <div id="edit-day-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit Safari Day</h3>
            <form id="edit-day-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Location *</label>
                        <input type="text" id="edit-day-location" name="location" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lodge</label>
                        <input type="text" id="edit-day-lodge" name="lodge" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Meal Plan</label>
                        <select id="edit-day-meal-plan" name="meal_plan" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">None</option>
                            <option value="BB">BB (Bed & Breakfast)</option>
                            <option value="HB">HB (Half Board)</option>
                            <option value="FB">FB (Full Board)</option>
                            <option value="AI">AI (All Inclusive)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Drink Plan</label>
                        <select id="edit-day-drink-plan" name="drink_plan" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">None</option>
                            <option value="Local Drinks">Local Drinks</option>
                            <option value="Premium Drinks">Premium Drinks</option>
                            <option value="All Drinks">All Drinks</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" onclick="document.getElementById('edit-day-modal').classList.add('hidden')" />
                    <x-action-button type="save" label="Save Changes" :submit="true" />
                </div>
            </form>
        </div>
    </div>

    <script>
        // Rate Edit show/hide functions
        function showRateEdit(paymentId) {
            document.querySelector('.rate-display-' + paymentId).classList.add('hidden');
            document.querySelector('.rate-edit-' + paymentId).classList.remove('hidden');
        }
        function hideRateEdit(paymentId) {
            document.querySelector('.rate-display-' + paymentId).classList.remove('hidden');
            document.querySelector('.rate-edit-' + paymentId).classList.add('hidden');
        }

        // Tab switching with URL hash persistence
        function switchToTab(tabId) {
            // Remove active from all tabs
            document.querySelectorAll('#booking-tabs .tab').forEach(t => t.classList.remove('active'));
            // Remove active from all content
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Find and activate the tab button
            const tabButton = document.querySelector(`#booking-tabs .tab[data-tab="${tabId}"]`);
            if (tabButton) {
                tabButton.classList.add('active');
            }
            // Show corresponding content
            const tabContent = document.getElementById(tabId);
            if (tabContent) {
                tabContent.classList.add('active');
            }

            // Update URL hash without triggering scroll
            history.replaceState(null, null, `#${tabId}`);
        }

        document.querySelectorAll('#booking-tabs .tab').forEach(tab => {
            tab.addEventListener('click', function() {
                switchToTab(this.dataset.tab);
            });
        });

        // On page load, check URL query param or hash and activate that tab
        document.addEventListener('DOMContentLoaded', function() {
            // Check for ?tab= query parameter first (from redirects)
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                const tabContent = document.getElementById(tabParam);
                if (tabContent && tabContent.classList.contains('tab-content')) {
                    switchToTab(tabParam);
                    // Clean up URL by removing query param and adding hash instead
                    const newUrl = window.location.pathname + '#' + tabParam;
                    history.replaceState(null, null, newUrl);
                }
            } else {
                // Fall back to URL hash
                const hash = window.location.hash.substring(1);
                if (hash) {
                    const tabContent = document.getElementById(hash);
                    if (tabContent && tabContent.classList.contains('tab-content')) {
                        switchToTab(hash);
                    }
                }
            }

            // Preserve tab hash on form submissions by adding hidden input
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const currentHash = window.location.hash.substring(1);
                    if (currentHash) {
                        // Add hidden input for tab preservation
                        let tabInput = form.querySelector('input[name="_tab"]');
                        if (!tabInput) {
                            tabInput = document.createElement('input');
                            tabInput.type = 'hidden';
                            tabInput.name = '_tab';
                            form.appendChild(tabInput);
                        }
                        tabInput.value = currentHash;
                    }
                });
            });
        });

        // Add Traveler Modal
        function openAddTravelerModal(groupId) {
            const form = document.getElementById('add-traveler-form');
            form.action = `/groups/${groupId}/travelers`;
            document.getElementById('add-traveler-modal').classList.remove('hidden');
        }

        // Add Addon - update form action when traveler selected
        function updateAddonFormAction() {
            const travelerId = document.getElementById('addon-traveler-select').value;
            const form = document.getElementById('add-addon-form');
            if (travelerId) {
                form.action = `/travelers/${travelerId}/addons`;
            }
        }

        function updateAddonLabels() {
            const type = document.getElementById('addon-type-select').value;
            const nameLabel = document.getElementById('addon-name-label');
            const nameInput = document.getElementById('addon-name-input');
            const costLabel = document.getElementById('addon-cost-label');
            const submitBtn = document.getElementById('addon-submit-btn');

            if (type === 'credit') {
                nameLabel.textContent = 'Credit Description';
                nameInput.placeholder = 'e.g., Hotel change credit, Day removed';
                costLabel.textContent = 'Credit Amount ($)';
                submitBtn.textContent = 'Add Credit';
            } else {
                nameLabel.textContent = 'Experience Name';
                nameInput.placeholder = 'e.g., Balloon Safari, Spa Treatment';
                costLabel.textContent = 'Cost Per Person ($)';
                submitBtn.textContent = 'Add Experience';
            }
        }

        // Edit Traveler Modal
        function openEditTravelerModal(travelerId, firstName, lastName, email, phone, dob, isLead) {
            const form = document.getElementById('edit-traveler-form');
            form.action = `/travelers/${travelerId}`;
            document.getElementById('edit-traveler-first-name').value = firstName;
            document.getElementById('edit-traveler-last-name').value = lastName;
            document.getElementById('edit-traveler-email').value = email || '';
            document.getElementById('edit-traveler-phone').value = phone || '';
            document.getElementById('edit-traveler-dob').value = dob || '';
            document.getElementById('edit-traveler-is-lead').checked = isLead;
            document.getElementById('edit-traveler-modal').classList.remove('hidden');
        }

        // Edit Day Modal
        function openEditDayModal(dayId) {
            // Find the day data from the page
            const dayRow = document.querySelector(`tr[data-day-id="${dayId}"]`);
            if (!dayRow) {
                // Fallback: find by Safari day data in the page
                const allDays = @json($booking->safariDays->map(function($day) {
                    return [
                        'id' => $day->id,
                        'location' => $day->location,
                        'lodge' => $day->lodge,
                        'meal_plan' => $day->meal_plan,
                        'drink_plan' => $day->drink_plan,
                    ];
                }));

                const dayData = allDays.find(day => day.id === dayId);
                if (dayData) {
                    populateEditDayModal(dayId, dayData);
                }
            }
        }

        function populateEditDayModal(dayId, dayData) {
            const form = document.getElementById('edit-day-form');
            form.action = `/safari-days/${dayId}`;

            document.getElementById('edit-day-location').value = dayData.location || '';
            document.getElementById('edit-day-lodge').value = dayData.lodge || '';
            document.getElementById('edit-day-meal-plan').value = dayData.meal_plan || '';
            document.getElementById('edit-day-drink-plan').value = dayData.drink_plan || '';

            document.getElementById('edit-day-modal').classList.remove('hidden');
        }

        // Add Flight Modal
        function openAddFlightModal(travelerId) {
            const form = document.getElementById('add-flight-form');
            form.action = `/travelers/${travelerId}/flights`;

            // Reset form fields
            document.getElementById('copy-from-traveler').value = '';
            document.getElementById('flight-type-select').value = 'arrival';
            document.getElementById('flight-airport').value = '';
            document.getElementById('flight-number').value = '';
            document.getElementById('flight-date').value = '';
            document.getElementById('flight-time').value = '';
            toggleFlightInstructions();

            document.getElementById('add-flight-modal').classList.remove('hidden');
        }

        function copyFlightFromTraveler(flightId) {
            if (!flightId) return;

            const select = document.getElementById('copy-from-traveler');
            const option = select.options[select.selectedIndex];

            document.getElementById('flight-type-select').value = option.dataset.type || 'arrival';
            document.getElementById('flight-airport').value = option.dataset.airport || '';
            document.getElementById('flight-number').value = option.dataset.flightNumber || '';
            document.getElementById('flight-date').value = option.dataset.date || '';
            document.getElementById('flight-time').value = option.dataset.time || '';

            toggleFlightInstructions();
        }

        // Copy Flight Modal
        function openCopyFlightModal(flightId, flightType, airport, flightNumber, currentTravelerId) {
            const form = document.getElementById('copy-flight-form');
            form.action = `/flights/${flightId}/copy`;

            // Update info text
            const typeLabel = flightType === 'arrival' ? 'Arrival' : 'Departure';
            const flightInfo = flightNumber ? `${typeLabel} - ${airport} (${flightNumber})` : `${typeLabel} - ${airport}`;
            document.getElementById('copy-flight-info').textContent = flightInfo;

            // Reset all checkboxes and hide the current traveler's option
            document.querySelectorAll('.copy-flight-traveler-option').forEach(option => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                checkbox.checked = false;

                // Hide the option for the traveler who already has this flight
                if (parseInt(option.dataset.travelerId) === currentTravelerId) {
                    option.classList.add('hidden');
                } else {
                    option.classList.remove('hidden');
                }
            });

            document.getElementById('copy-flight-modal').classList.remove('hidden');
        }

        function selectAllTravelers() {
            document.querySelectorAll('.copy-flight-traveler-option:not(.hidden) input[type="checkbox"]').forEach(cb => {
                cb.checked = true;
            });
        }

        function deselectAllTravelers() {
            document.querySelectorAll('.copy-flight-traveler-option input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });
        }

        // Payment Reminder Modal
        function openPaymentReminderModal(bookingId, travelerId, travelerName, totalCost) {
            const form = document.getElementById('payment-reminder-form');
            form.action = `/bookings/${bookingId}/travelers/${travelerId}/email/payment-reminder`;
            document.getElementById('payment-reminder-recipient').textContent = travelerName;
            document.getElementById('payment-reminder-amount').value = totalCost > 0 ? (totalCost * 0.25).toFixed(2) : '';
            document.getElementById('payment-reminder-modal').classList.remove('hidden');
        }

        // Close modals when clicking outside
        document.querySelectorAll('[id$="-modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });

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

        // Alpine.js component for editing safari day activities
        function activityCell(safariDayId, period, initialActivities) {
            return {
                safariDayId: safariDayId,
                period: period,
                activities: initialActivities || [],
                editing: false,
                editText: '',

                startEdit() {
                    this.editText = this.activities.join('\n');
                    this.editing = true;
                },

                cancel() {
                    this.editing = false;
                    this.editText = '';
                },

                async save() {
                    const activities = this.editText.split('\n').map(a => a.trim()).filter(a => a);

                    try {
                        const response = await fetch(`/safari-days/${this.safariDayId}/activities`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                period: this.period,
                                activities: activities
                            })
                        });

                        if (response.ok) {
                            this.activities = activities;
                            this.editing = false;
                        } else {
                            alert('Failed to save activities');
                        }
                    } catch (error) {
                        console.error('Error saving activities:', error);
                        alert('Failed to save activities');
                    }
                }
            };
        }

    </script>
</x-app-layout>
