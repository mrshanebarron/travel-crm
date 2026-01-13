<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit {{ $transfer->transfer_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Transfer Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Transfer Details</h3>

                        <form method="POST" action="{{ route('transfers.update', $transfer) }}">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <div>
                                    <label for="request_date" class="block text-sm font-medium text-gray-700">Request Date</label>
                                    <input type="date" name="request_date" id="request_date" value="{{ $transfer->request_date->format('Y-m-d') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="draft" {{ $transfer->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="sent" {{ $transfer->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                        <option value="transfer_completed" {{ $transfer->status === 'transfer_completed' ? 'selected' : '' }}>Transfer Completed</option>
                                        <option value="vendor_payments_completed" {{ $transfer->status === 'vendor_payments_completed' ? 'selected' : '' }}>Vendor Payments Completed</option>
                                    </select>
                                </div>

                                <div class="pt-4">
                                    <div class="text-sm text-gray-500">Total Amount</div>
                                    <div class="text-2xl font-bold text-gray-900">${{ number_format($transfer->total_amount, 2) }}</div>
                                </div>

                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Expenses</h3>

                        <!-- Add Expense Form -->
                        <form method="POST" action="{{ route('transfer-expenses.store', $transfer) }}" class="mb-6 p-4 bg-gray-50 rounded-lg">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                                <select name="booking_id" class="rounded-md border-gray-300 text-sm" required>
                                    <option value="">Select Booking</option>
                                    @php
                                        $bookings = \App\Models\Booking::where('status', '!=', 'completed')->orderBy('booking_number')->get();
                                    @endphp
                                    @foreach($bookings as $booking)
                                        <option value="{{ $booking->id }}">{{ $booking->booking_number }}</option>
                                    @endforeach
                                </select>
                                <select name="category" class="rounded-md border-gray-300 text-sm" required>
                                    <option value="lodge">Lodge</option>
                                    <option value="guide_vehicle">Guide/Vehicle</option>
                                    <option value="park_entry">Park Entry</option>
                                    <option value="misc">Misc</option>
                                </select>
                                <input type="text" name="vendor_name" placeholder="Vendor" class="rounded-md border-gray-300 text-sm">
                                <select name="payment_type" class="rounded-md border-gray-300 text-sm" required>
                                    <option value="deposit">Deposit</option>
                                    <option value="final">Final</option>
                                    <option value="other">Other</option>
                                </select>
                                <input type="number" name="amount" placeholder="Amount" step="0.01" min="0" class="rounded-md border-gray-300 text-sm" required>
                                <button type="submit" class="bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Add</button>
                            </div>
                            <div class="mt-2">
                                <input type="text" name="notes" placeholder="Notes (optional)" class="w-full rounded-md border-gray-300 text-sm">
                            </div>
                        </form>

                        @if($transfer->expenses->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($transfer->expenses as $expense)
                                        <tr>
                                            <td class="px-3 py-2 text-sm">{{ $expense->booking->booking_number }}</td>
                                            <td class="px-3 py-2 text-sm">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</td>
                                            <td class="px-3 py-2 text-sm">{{ $expense->vendor_name ?? '-' }}</td>
                                            <td class="px-3 py-2 text-sm text-right font-medium">${{ number_format($expense->amount, 2) }}</td>
                                            <td class="px-3 py-2 text-sm text-right">
                                                <form method="POST" action="{{ route('transfer-expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('Remove this expense?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-3 py-2 text-right font-medium">Total:</td>
                                        <td class="px-3 py-2 text-right font-bold">${{ number_format($transfer->total_amount, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <p class="text-gray-500 text-center py-4">No expenses added yet</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-between">
                <form method="POST" action="{{ route('transfers.destroy', $transfer) }}" onsubmit="return confirm('Delete this transfer request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest bg-white hover:bg-red-50">
                        Delete Transfer
                    </button>
                </form>

                <a href="{{ route('transfers.show', $transfer) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                    View Transfer
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
