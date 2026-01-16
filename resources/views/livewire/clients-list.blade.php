<div>
    <!-- Search -->
    <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search by name, email, or booking number..."
                    class="w-full pl-10 pr-4 py-2 rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            @if($search)
                <x-action-button type="clear" wire:click="$set('search', '')" class="w-full sm:w-auto justify-center" />
            @endif
        </div>
        <div class="mt-3 text-sm text-slate-500">
            {{ $travelers->total() }} client{{ $travelers->total() !== 1 ? 's' : '' }}
        </div>
    </div>

    <!-- Clients -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($travelers as $traveler)
                <div class="block p-4" wire:key="mobile-client-{{ $traveler->id }}">
                    <button wire:click="openViewModal({{ $traveler->id }})" class="w-full text-left hover:bg-orange-50 transition-colors rounded-lg -m-2 p-2">
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
                    </button>
                    <div class="mt-3 flex items-center gap-2 ml-13">
                        <x-action-button type="view" size="sm" wire:click="openViewModal({{ $traveler->id }})" class="flex-1 justify-center" />
                        <x-action-button type="edit" size="sm" wire:click="openEditModal({{ $traveler->id }})" class="flex-1 justify-center" />
                    </div>
                </div>
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
                        <tr class="hover:bg-slate-50 cursor-pointer" wire:key="desktop-client-{{ $traveler->id }}" wire:click="openViewModal({{ $traveler->id }})">
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
                                    <a href="{{ route('bookings.show', $traveler->group->booking) }}" wire:click.stop
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
                            <td wire:click.stop>
                                <div class="flex items-center gap-2">
                                    <x-action-button type="view" size="xs" wire:click="openViewModal({{ $traveler->id }})" />
                                    <x-action-button type="edit" size="xs" wire:click="openEditModal({{ $traveler->id }})" />
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

    <!-- View Client Modal -->
    @if($showViewModal && $viewTraveler)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="closeViewModal">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold text-slate-900">
                        {{ $viewTraveler->first_name }} {{ $viewTraveler->last_name }}
                        @if($viewTraveler->is_lead)
                            <span class="text-sm text-orange-600">(Lead Traveler)</span>
                        @endif
                    </h3>
                    <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Contact Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                            <p class="text-slate-900">{{ $viewTraveler->email ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                            <p class="text-slate-900">{{ $viewTraveler->phone ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</label>
                            <p class="text-slate-900">{{ $viewTraveler->dob?->format('M j, Y') ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Booking</label>
                            @if($viewTraveler->group && $viewTraveler->group->booking)
                                <p>
                                    <a href="{{ route('bookings.show', $viewTraveler->group->booking) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                                        {{ $viewTraveler->group->booking->booking_number }}
                                    </a>
                                </p>
                            @else
                                <p class="text-slate-900">-</p>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Info -->
                    @if($viewTraveler->payment)
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-3">Payment Information</h4>
                            <div class="bg-slate-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <label class="text-xs text-slate-500">Safari Rate</label>
                                        <p class="font-medium">${{ number_format($viewTraveler->payment->safari_rate, 2) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500">Deposit (25%)</label>
                                        <p class="font-medium {{ $viewTraveler->payment->deposit_paid ? 'text-green-600' : '' }}">
                                            ${{ number_format($viewTraveler->payment->deposit, 2) }}
                                            @if($viewTraveler->payment->deposit_paid) <span class="text-xs">(Paid)</span> @endif
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500">90-Day (25%)</label>
                                        <p class="font-medium {{ $viewTraveler->payment->payment_90_day_paid ? 'text-green-600' : '' }}">
                                            ${{ number_format($viewTraveler->payment->payment_90_day, 2) }}
                                            @if($viewTraveler->payment->payment_90_day_paid) <span class="text-xs">(Paid)</span> @endif
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500">45-Day (50%)</label>
                                        <p class="font-medium {{ $viewTraveler->payment->payment_45_day_paid ? 'text-green-600' : '' }}">
                                            ${{ number_format($viewTraveler->payment->payment_45_day, 2) }}
                                            @if($viewTraveler->payment->payment_45_day_paid) <span class="text-xs">(Paid)</span> @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Flights -->
                    @if($viewTraveler->flights && $viewTraveler->flights->count() > 0)
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-3">Flight Information</h4>
                            <div class="space-y-2">
                                @foreach($viewTraveler->flights as $flight)
                                    <div class="bg-slate-50 rounded-lg p-3 text-sm">
                                        <span class="badge badge-info text-xs mb-1">{{ ucfirst($flight->type) }}</span>
                                        <p class="font-medium">{{ $flight->airline }} {{ $flight->flight_number }}</p>
                                        <p class="text-slate-500">{{ $flight->departure_date?->format('M j, Y') }} at {{ $flight->departure_time }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                    <x-action-button type="cancel" label="Close" wire:click="closeViewModal" />
                    <x-action-button type="edit" wire:click="openEditModal({{ $viewTraveler->id }})" />
                    @if($viewTraveler->group && $viewTraveler->group->booking)
                        <x-action-button type="view" label="View Booking" :href="route('bookings.show', $viewTraveler->group->booking)" />
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Client Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="closeEditModal">
            <div class="bg-white rounded-xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Edit Client</h3>
                    <button wire:click="closeEditModal" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="updateTraveler">
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">First Name</label>
                                <input type="text" wire:model="editFirstName" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                                @error('editFirstName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Last Name</label>
                                <input type="text" wire:model="editLastName" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                                @error('editLastName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                            <input type="email" wire:model="editEmail" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('editEmail') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                            <input type="text" wire:model="editPhone" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('editPhone') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</label>
                            <input type="date" wire:model="editDob" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('editDob') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="editIsLead" id="editIsLead" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                            <label for="editIsLead" class="text-sm text-slate-700">Lead Traveler</label>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                        <x-action-button type="cancel" wire:click="closeEditModal" />
                        <x-action-button type="save" label="Save Changes" :submit="true" />
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
