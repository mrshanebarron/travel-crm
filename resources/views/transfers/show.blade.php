<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('transfers.index') }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Transfers
        </a>
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $transfer->transfer_number }}</h1>
                <p class="text-slate-500 text-sm sm:text-base">Request Date: {{ $transfer->request_date->format('M j, Y') }}</p>
                <div class="flex items-center gap-2 mt-2 sm:hidden">
                    @if($transfer->status === 'draft')
                        <span class="badge" style="background: #f1f5f9; color: #475569;">Draft</span>
                    @elseif($transfer->status === 'sent')
                        <span class="badge badge-info">Sent</span>
                    @elseif($transfer->status === 'transfer_completed')
                        <span class="badge badge-warning">Transfer Complete</span>
                    @else
                        <span class="badge badge-success">Payments Complete</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block">
                    @if($transfer->status === 'draft')
                        <span class="badge" style="background: #f1f5f9; color: #475569;">Draft</span>
                    @elseif($transfer->status === 'sent')
                        <span class="badge badge-info">Sent</span>
                    @elseif($transfer->status === 'transfer_completed')
                        <span class="badge badge-warning">Transfer Complete</span>
                    @else
                        <span class="badge badge-success">Payments Complete</span>
                    @endif
                </div>
                <a href="{{ route('transfers.edit', $transfer) }}" class="btn btn-secondary w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>

    <!-- Workflow Progress -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-6 sm:mb-8 overflow-x-auto">
        <div class="flex items-center justify-between min-w-[500px]">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transfer->status !== 'draft' ? 'bg-green-500 text-white' : 'bg-orange-500 text-white' }}">
                    @if($transfer->status !== 'draft')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <span class="text-sm font-bold">1</span>
                    @endif
                </div>
                <span class="font-medium {{ $transfer->status === 'draft' ? 'text-orange-600' : 'text-green-600' }}">Draft</span>
            </div>
            <div class="flex-1 h-1 mx-4 {{ in_array($transfer->status, ['sent', 'transfer_completed', 'vendor_payments_completed']) ? 'bg-green-500' : 'bg-slate-200' }}"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ in_array($transfer->status, ['transfer_completed', 'vendor_payments_completed']) ? 'bg-green-500 text-white' : ($transfer->status === 'sent' ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                    @if(in_array($transfer->status, ['transfer_completed', 'vendor_payments_completed']))
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <span class="text-sm font-bold">2</span>
                    @endif
                </div>
                <span class="font-medium {{ $transfer->status === 'sent' ? 'text-orange-600' : (in_array($transfer->status, ['transfer_completed', 'vendor_payments_completed']) ? 'text-green-600' : 'text-slate-500') }}">Sent</span>
            </div>
            <div class="flex-1 h-1 mx-4 {{ in_array($transfer->status, ['transfer_completed', 'vendor_payments_completed']) ? 'bg-green-500' : 'bg-slate-200' }}"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transfer->status === 'vendor_payments_completed' ? 'bg-green-500 text-white' : ($transfer->status === 'transfer_completed' ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                    @if($transfer->status === 'vendor_payments_completed')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <span class="text-sm font-bold">3</span>
                    @endif
                </div>
                <span class="font-medium {{ $transfer->status === 'transfer_completed' ? 'text-orange-600' : ($transfer->status === 'vendor_payments_completed' ? 'text-green-600' : 'text-slate-500') }}">Transfer Done</span>
            </div>
            <div class="flex-1 h-1 mx-4 {{ $transfer->status === 'vendor_payments_completed' ? 'bg-green-500' : 'bg-slate-200' }}"></div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transfer->status === 'vendor_payments_completed' ? 'bg-green-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                    @if($transfer->status === 'vendor_payments_completed')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <span class="text-sm font-bold">4</span>
                    @endif
                </div>
                <span class="font-medium {{ $transfer->status === 'vendor_payments_completed' ? 'text-green-600' : 'text-slate-500' }}">Completed</span>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <div class="text-xs sm:text-sm text-slate-500 mb-1">Total Amount</div>
            <div class="text-lg sm:text-2xl font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <div class="text-xs sm:text-sm text-slate-500 mb-1">Expenses</div>
            <div class="text-lg sm:text-2xl font-bold text-slate-900">{{ $transfer->expenses->count() }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <div class="text-xs sm:text-sm text-slate-500 mb-1">Created By</div>
            <div class="text-sm sm:text-lg font-medium text-slate-900 truncate">{{ $transfer->creator->name }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
            <div class="text-xs sm:text-sm text-slate-500 mb-1">Bookings</div>
            <div class="text-lg sm:text-2xl font-bold text-slate-900">{{ $transfer->expenses->pluck('booking_id')->unique()->count() }}</div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6 sm:mb-8">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900">Expenses</h2>
        </div>

        @if($transfer->expenses->count() > 0)
            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-slate-100">
                @foreach($transfer->expenses as $expense)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div>
                                @if($expense->booking)
                                    <a href="{{ route('bookings.show', $expense->booking) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                                        {{ $expense->booking->booking_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                                <p class="text-sm text-slate-500">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</p>
                            </div>
                            <span class="text-lg font-bold text-slate-900">${{ number_format($expense->amount, 2) }}</span>
                        </div>
                        <p class="text-sm text-slate-900">{{ $expense->description }}</p>
                        @if($expense->vendor_name)
                            <p class="text-sm text-slate-600 mt-1">{{ $expense->vendor_name }}</p>
                        @endif
                    </div>
                @endforeach
                <div class="p-4 bg-slate-50">
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
                            <th>Description</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfer->expenses as $expense)
                            <tr>
                                <td>
                                    @if($expense->booking)
                                        <a href="{{ route('bookings.show', $expense->booking) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                                            {{ $expense->booking->booking_number }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="text-slate-900">{{ $expense->description }}</td>
                                <td class="text-slate-600">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</td>
                                <td class="text-slate-600">{{ $expense->vendor_name ?? '-' }}</td>
                                <td class="text-right font-medium text-slate-900">${{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="4" class="text-right font-semibold text-slate-900">Total:</td>
                            <td class="text-right font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-8 sm:p-12 text-center">
                <p class="text-slate-500">No expenses added yet.</p>
                <a href="{{ route('transfers.edit', $transfer) }}" class="text-orange-600 hover:text-orange-800 font-medium">Add expenses</a>
            </div>
        @endif
    </div>

    <!-- Associated Tasks -->
    @if($transfer->transferTask || $transfer->vendorTask)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6 sm:mb-8">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Associated Tasks</h2>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-4">
                    @if($transfer->transferTask)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 sm:p-4 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $transfer->transferTask->status === 'completed' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                    @if($transfer->transferTask->status === 'completed')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <span class="text-xs font-bold">1</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">{{ $transfer->transferTask->name }}</div>
                                    <div class="text-sm text-slate-500">
                                        @if($transfer->transferTask->status === 'completed')
                                            Completed {{ $transfer->transferTask->completed_at->format('M j, Y') }}
                                        @else
                                            Due {{ $transfer->transferTask->due_date ? $transfer->transferTask->due_date->format('M j, Y') : 'No date set' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="badge {{ $transfer->transferTask->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($transfer->transferTask->status) }}
                            </span>
                        </div>
                    @endif

                    @if($transfer->vendorTask)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 sm:p-4 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $transfer->vendorTask->status === 'completed' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                    @if($transfer->vendorTask->status === 'completed')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <span class="text-xs font-bold">2</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">{{ $transfer->vendorTask->name }}</div>
                                    <div class="text-sm text-slate-500">
                                        @if($transfer->vendorTask->status === 'completed')
                                            Completed {{ $transfer->vendorTask->completed_at->format('M j, Y') }}
                                        @else
                                            Due {{ $transfer->vendorTask->due_date ? $transfer->vendorTask->due_date->format('M j, Y') : 'No date set' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="badge {{ $transfer->vendorTask->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($transfer->vendorTask->status) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Timeline -->
    @if($transfer->sent_at || $transfer->transfer_completed_at || $transfer->vendor_payments_completed_at)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Timeline</h2>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-slate-400 rounded-full"></div>
                        <div>
                            <span class="font-medium text-slate-900">Created</span>
                            <span class="text-sm text-slate-500 ml-2">{{ $transfer->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                    </div>
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
                                <span class="badge badge-success ml-2">Ledger entries posted</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
