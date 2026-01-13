<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $transfer->transfer_number }}
                </h2>
                <p class="text-sm text-gray-500">Request Date: {{ $transfer->request_date->format('M j, Y') }}</p>
            </div>
            <div class="flex gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $transfer->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $transfer->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $transfer->status === 'transfer_completed' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $transfer->status === 'vendor_payments_completed' ? 'bg-green-100 text-green-800' : '' }}">
                    {{ str_replace('_', ' ', ucfirst($transfer->status)) }}
                </span>
                <a href="{{ route('transfers.edit', $transfer) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Total Amount</div>
                            <div class="text-2xl font-bold text-gray-900">${{ number_format($transfer->total_amount, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Expenses</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $transfer->expenses->count() }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Created By</div>
                            <div class="text-lg text-gray-900">{{ $transfer->creator->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Bookings</div>
                            <div class="text-lg text-gray-900">{{ $transfer->expenses->pluck('booking_id')->unique()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Expenses</h3>

                    @if($transfer->expenses->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($transfer->expenses as $expense)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('bookings.show', $expense->booking) }}" class="text-indigo-600 hover:text-indigo-800">
                                                {{ $expense->booking->booking_number }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $expense->vendor_name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ ucfirst($expense->payment_type) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-medium">${{ number_format($expense->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ Str::limit($expense->notes, 30) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-900">Total:</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($transfer->total_amount, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <p class="text-gray-500 text-center py-8">No expenses added yet. <a href="{{ route('transfers.edit', $transfer) }}" class="text-indigo-600 hover:text-indigo-800">Add expenses</a></p>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            @if($transfer->sent_at || $transfer->transfer_completed_at || $transfer->vendor_payments_completed_at)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-3">
                        @if($transfer->sent_at)
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <div>
                                    <span class="font-medium">Sent to bank</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ $transfer->sent_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                        @endif
                        @if($transfer->transfer_completed_at)
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <div>
                                    <span class="font-medium">Transfer completed</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ $transfer->transfer_completed_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                        @endif
                        @if($transfer->vendor_payments_completed_at)
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <div>
                                    <span class="font-medium">Vendor payments completed</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ $transfer->vendor_payments_completed_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
