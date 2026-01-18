<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('transfers.show', $transfer) }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Transfer
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Edit {{ $transfer->transfer_number }}</h1>
        <p class="text-slate-500 text-sm sm:text-base">Update transfer details and manage expenses</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Transfer Details -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Transfer Details</h3>
            </div>
            <div class="p-4 sm:p-6">
                <form method="POST" action="{{ route('transfers.update', $transfer) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label for="request_date" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Request Date *</label>
                            <input type="date" name="request_date" id="request_date" value="{{ $transfer->request_date->format('Y-m-d') }}"
                                class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                        </div>

                        @if($transfer->status !== 'draft')
                        <div>
                            <label for="status" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Status *</label>
                            <select name="status" id="status" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                                <option value="sent" {{ $transfer->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="transfer_completed" {{ $transfer->status === 'transfer_completed' ? 'selected' : '' }}>Transfer Completed</option>
                                <option value="vendor_payments_completed" {{ $transfer->status === 'vendor_payments_completed' ? 'selected' : '' }}>Vendor Payments Completed</option>
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="status" value="draft">
                        @endif

                        <div class="pt-4 border-t border-slate-200">
                            <div class="text-sm text-slate-500">Total Amount</div>
                            <div class="text-2xl font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</div>
                        </div>

                        @if($transfer->status === 'draft')
                            <x-action-button type="save" label="Save Draft" :submit="true" class="w-full justify-center" />
                        @else
                            <x-action-button type="save" label="Save Changes" :submit="true" class="w-full justify-center" />
                        @endif
                    </div>
                </form>

                @if($transfer->status === 'draft' && $transfer->expenses->count() > 0)
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <form method="POST" action="{{ route('transfers.send', $transfer) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-colors"
                            onclick="return confirm('Send this transfer request? This will create a task to make the transfer.')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Send Transfer
                        </button>
                    </form>
                    <p class="text-xs text-slate-500 mt-2 text-center">Sending will create a task to make the transfer</p>
                </div>
                @endif

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <form method="POST" action="{{ route('transfers.destroy', $transfer) }}" onsubmit="return confirm('Delete this transfer request? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <x-action-button type="delete" label="Delete Transfer" :submit="true" class="w-full justify-center" />
                    </form>
                </div>
            </div>
        </div>

        <!-- Expenses -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Expenses</h3>
            </div>
            <div class="p-4 sm:p-6">
                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="text-red-800 font-medium mb-2">Please fix the following errors:</div>
                        <ul class="list-disc list-inside text-red-700 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Add Expense Form -->
                <form method="POST" action="{{ route('transfer-expenses.store', $transfer) }}" class="mb-6 p-3 sm:p-4 bg-slate-50 rounded-lg">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <select name="booking_id" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('booking_id') border-red-500 @enderror" required>
                            <option value="">Select Booking</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>{{ $booking->display_name }}</option>
                            @endforeach
                        </select>
                        <select name="category" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('category') border-red-500 @enderror" required>
                            <option value="lodges_camps" {{ old('category') == 'lodges_camps' ? 'selected' : '' }}>Lodges/Camps</option>
                            <option value="driver_guide" {{ old('category') == 'driver_guide' ? 'selected' : '' }}>Driver/Guide</option>
                            <option value="park_entry" {{ old('category') == 'park_entry' ? 'selected' : '' }}>Park Entry</option>
                            <option value="arrival_dept_flight" {{ old('category') == 'arrival_dept_flight' ? 'selected' : '' }}>Arrival/Dept Flight</option>
                            <option value="internal_flights" {{ old('category') == 'internal_flights' ? 'selected' : '' }}>Internal Flights</option>
                            <option value="driver_guide_invoices" {{ old('category') == 'driver_guide_invoices' ? 'selected' : '' }}>Driver/Guide Invoices</option>
                            <option value="misc" {{ old('category') == 'misc' ? 'selected' : '' }}>Misc</option>
                        </select>
                        <input type="text" name="vendor_name" placeholder="Vendor" value="{{ old('vendor_name') }}" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        <select name="payment_type" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('payment_type') border-red-500 @enderror" required>
                            <option value="deposit" {{ old('payment_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                            <option value="final" {{ old('payment_type') == 'final' ? 'selected' : '' }}>Final</option>
                            <option value="other" {{ old('payment_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <input type="number" name="amount" placeholder="Amount" step="0.01" min="0" value="{{ old('amount') }}" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('amount') border-red-500 @enderror" required>
                        <x-action-button type="add" size="sm" label="Add Expense" :submit="true" class="w-full justify-center" />
                    </div>
                    <div class="mt-3">
                        <input type="text" name="notes" placeholder="Notes (optional)" value="{{ old('notes') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </form>

                @if($transfer->expenses->count() > 0)
                    <!-- Mobile Card View -->
                    <div class="md:hidden divide-y divide-slate-100">
                        @foreach($transfer->expenses as $expense)
                            <div class="py-3">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div>
                                        <a href="{{ route('bookings.show', $expense->booking) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                                            {{ $expense->booking->booking_number }}
                                        </a>
                                        <p class="text-sm text-slate-500">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</p>
                                    </div>
                                    <span class="text-lg font-bold text-slate-900">${{ number_format($expense->amount, 2) }}</span>
                                </div>
                                @if($expense->vendor_name)
                                    <p class="text-sm text-slate-600 mb-2">{{ $expense->vendor_name }}</p>
                                @endif
                                <form method="POST" action="{{ route('transfer-expenses.destroy', $expense) }}" onsubmit="return confirm('Remove this expense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                                </form>
                            </div>
                        @endforeach
                        <div class="py-3 bg-slate-50 -mx-4 px-4 sm:-mx-6 sm:px-6">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-slate-900">Total:</span>
                                <span class="text-xl font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden md:block">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Booking</th>
                                    <th>Category</th>
                                    <th>Vendor</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfer->expenses as $expense)
                                    <tr>
                                        <td>
                                            <a href="{{ route('bookings.show', $expense->booking) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                                                {{ $expense->booking->booking_number }}
                                            </a>
                                        </td>
                                        <td class="text-slate-900">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</td>
                                        <td class="text-slate-900">{{ $expense->vendor_name ?? '-' }}</td>
                                        <td class="text-right font-medium text-slate-900">${{ number_format($expense->amount, 2) }}</td>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('transfer-expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('Remove this expense?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td colspan="3" class="text-right font-semibold text-slate-900">Total:</td>
                                    <td class="text-right font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-slate-500 text-center py-8">No expenses added yet. Use the form above to add expenses.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
