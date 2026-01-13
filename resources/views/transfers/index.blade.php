<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Transfer Requests</h1>
            <p class="text-slate-500">Manage fund transfer requests to Kenya</p>
        </div>
        <a href="{{ route('transfers.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Transfer
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('transfers.index') }}" class="tab {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('transfers.index') }}?status=draft" class="tab {{ request('status') === 'draft' ? 'active' : '' }}">Draft</a>
            <a href="{{ route('transfers.index') }}?status=sent" class="tab {{ request('status') === 'sent' ? 'active' : '' }}">Sent</a>
            <a href="{{ route('transfers.index') }}?status=transfer_completed" class="tab {{ request('status') === 'transfer_completed' ? 'active' : '' }}">Transfer Complete</a>
            <a href="{{ route('transfers.index') }}?status=vendor_payments_completed" class="tab {{ request('status') === 'vendor_payments_completed' ? 'active' : '' }}">Payments Complete</a>
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Transfer #</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                    <th>Expenses</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                    <tr class="cursor-pointer hover:bg-slate-50">
                        <td>
                            <a href="{{ route('transfers.show', $transfer) }}" class="text-teal-600 hover:text-teal-700 font-medium">
                                {{ $transfer->transfer_number }}
                            </a>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $transfer->request_date->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            @if($transfer->status === 'draft')
                                <span class="badge" style="background: #f1f5f9; color: #475569;">Draft</span>
                            @elseif($transfer->status === 'sent')
                                <span class="badge badge-info">Sent</span>
                            @elseif($transfer->status === 'transfer_completed')
                                <span class="badge badge-warning">Transfer Complete</span>
                            @else
                                <span class="badge badge-success">Payments Complete</span>
                            @endif
                        </td>
                        <td class="text-right font-medium text-slate-900">
                            ${{ number_format($transfer->total_amount, 2) }}
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                {{ $transfer->expenses->count() }} items
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-secondary text-sm py-2 px-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Open
                                </a>
                                <a href="{{ route('transfers.edit', $transfer) }}" class="btn btn-secondary text-sm py-2 px-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            No transfer requests found. <a href="{{ route('transfers.create') }}" class="text-teal-600 hover:text-teal-700">Create your first transfer</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($transfers->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
