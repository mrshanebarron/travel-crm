<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('transfers.show', $transfer) }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit {{ $transfer->transfer_number }}</h1>
            <p class="text-slate-500">Update transfer details and manage expenses</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transfer Details -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Transfer Details</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('transfers.update', $transfer) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label for="request_date" class="block text-sm font-medium text-slate-700 mb-1">Request Date</label>
                            <input type="date" name="request_date" id="request_date" value="{{ $transfer->request_date->format('Y-m-d') }}"
                                class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                                <option value="draft" {{ $transfer->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ $transfer->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="transfer_completed" {{ $transfer->status === 'transfer_completed' ? 'selected' : '' }}>Transfer Completed</option>
                                <option value="vendor_payments_completed" {{ $transfer->status === 'vendor_payments_completed' ? 'selected' : '' }}>Vendor Payments Completed</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-slate-200">
                            <div class="text-sm text-slate-500">Total Amount</div>
                            <div class="text-2xl font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <form method="POST" action="{{ route('transfers.destroy', $transfer) }}" onsubmit="return confirm('Delete this transfer request? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary w-full text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Expenses -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Expenses</h3>
            </div>
            <div class="p-6">
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
                <form method="POST" action="{{ route('transfer-expenses.store', $transfer) }}" class="mb-6 p-4 bg-slate-50 rounded-lg">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <select name="booking_id" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('booking_id') border-red-500 @enderror" required>
                            <option value="">Select Booking</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>{{ $booking->display_name }}</option>
                            @endforeach
                        </select>
                        <select name="category" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('category') border-red-500 @enderror" required>
                            <option value="lodge" {{ old('category') == 'lodge' ? 'selected' : '' }}>Lodge</option>
                            <option value="guide_vehicle" {{ old('category') == 'guide_vehicle' ? 'selected' : '' }}>Guide/Vehicle</option>
                            <option value="park_entry" {{ old('category') == 'park_entry' ? 'selected' : '' }}>Park Entry</option>
                            <option value="misc" {{ old('category') == 'misc' ? 'selected' : '' }}>Misc</option>
                        </select>
                        <input type="text" name="vendor_name" placeholder="Vendor" value="{{ old('vendor_name') }}" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        <select name="payment_type" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('payment_type') border-red-500 @enderror" required>
                            <option value="deposit" {{ old('payment_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                            <option value="final" {{ old('payment_type') == 'final' ? 'selected' : '' }}>Final</option>
                            <option value="other" {{ old('payment_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <input type="number" name="amount" placeholder="Amount" step="0.01" min="0" value="{{ old('amount') }}" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500 @error('amount') border-red-500 @enderror" required>
                        <button type="submit" class="btn btn-primary text-sm">Add</button>
                    </div>
                    <div class="mt-3">
                        <input type="text" name="notes" placeholder="Notes (optional)" value="{{ old('notes') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </form>

                @if($transfer->expenses->count() > 0)
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
                                        <a href="{{ route('bookings.show', $expense->booking) }}" class="text-teal-600 hover:text-teal-800 font-medium">
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
                @else
                    <p class="text-slate-500 text-center py-8">No expenses added yet. Use the form above to add expenses.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
