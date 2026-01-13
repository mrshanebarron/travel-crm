<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('transfers.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $transfer->transfer_number }}</h1>
                <p class="text-slate-500">Request Date: {{ $transfer->request_date->format('M j, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($transfer->status === 'draft')
                <span class="badge" style="background: #f1f5f9; color: #475569;">Draft</span>
            @elseif($transfer->status === 'sent')
                <span class="badge badge-info">Sent</span>
            @elseif($transfer->status === 'transfer_completed')
                <span class="badge badge-warning">Transfer Complete</span>
            @else
                <span class="badge badge-success">Payments Complete</span>
            @endif
            <a href="{{ route('transfers.edit', $transfer) }}" class="btn btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Transfer
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Total Amount</div>
            <div class="text-2xl font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Expenses</div>
            <div class="text-2xl font-bold text-slate-900">{{ $transfer->expenses->count() }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Created By</div>
            <div class="text-lg font-medium text-slate-900">{{ $transfer->creator->name }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Bookings</div>
            <div class="text-2xl font-bold text-slate-900">{{ $transfer->expenses->pluck('booking_id')->unique()->count() }}</div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900">Expenses</h2>
        </div>

        @if($transfer->expenses->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Type</th>
                        <th class="text-right">Amount</th>
                        <th>Notes</th>
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
                            <td class="text-slate-500">{{ ucfirst($expense->payment_type) }}</td>
                            <td class="text-right font-medium text-slate-900">${{ number_format($expense->amount, 2) }}</td>
                            <td class="text-slate-500">{{ Str::limit($expense->notes, 30) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50">
                        <td colspan="4" class="text-right font-semibold text-slate-900">Total:</td>
                        <td class="text-right font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="p-12 text-center">
                <p class="text-slate-500">No expenses added yet.</p>
                <a href="{{ route('transfers.edit', $transfer) }}" class="text-teal-600 hover:text-teal-700 font-medium">Add expenses</a>
            </div>
        @endif
    </div>

    <!-- Timeline -->
    @if($transfer->sent_at || $transfer->transfer_completed_at || $transfer->vendor_payments_completed_at)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Timeline</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if($transfer->sent_at)
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <div>
                                <span class="font-medium text-slate-900">Sent to bank</span>
                                <span class="text-sm text-slate-500 ml-2">{{ $transfer->sent_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    @endif
                    @if($transfer->transfer_completed_at)
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                            <div>
                                <span class="font-medium text-slate-900">Transfer completed</span>
                                <span class="text-sm text-slate-500 ml-2">{{ $transfer->transfer_completed_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    @endif
                    @if($transfer->vendor_payments_completed_at)
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div>
                                <span class="font-medium text-slate-900">Vendor payments completed</span>
                                <span class="text-sm text-slate-500 ml-2">{{ $transfer->vendor_payments_completed_at->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
