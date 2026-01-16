<div>
    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <div class="flex items-center gap-2 sm:gap-4 overflow-x-auto pb-1 -mb-1">
            <button wire:click="setStatus('')" class="tab {{ !$status ? 'active' : '' }}">All</button>
            <button wire:click="setStatus('draft')" class="tab {{ $status === 'draft' ? 'active' : '' }}">Draft</button>
            <button wire:click="setStatus('sent')" class="tab {{ $status === 'sent' ? 'active' : '' }}">Sent</button>
            <button wire:click="setStatus('transfer_completed')" class="tab {{ $status === 'transfer_completed' ? 'active' : '' }}">Transfer Done</button>
            <button wire:click="setStatus('vendor_payments_completed')" class="tab {{ $status === 'vendor_payments_completed' ? 'active' : '' }}">Paid</button>
        </div>
    </div>

    <!-- Transfers -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($transfers as $transfer)
                <a href="{{ route('transfers.show', $transfer) }}" class="block p-4 hover:bg-orange-50 transition-colors" wire:key="mobile-transfer-{{ $transfer->id }}">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div>
                            <p class="font-semibold text-orange-600">{{ $transfer->transfer_number }}</p>
                            <p class="text-sm text-slate-500">{{ $transfer->request_date->format('M d, Y') }}</p>
                        </div>
                        @if($transfer->status === 'draft')
                            <span class="badge text-xs" style="background: #f1f5f9; color: #475569;">Draft</span>
                        @elseif($transfer->status === 'sent')
                            <span class="badge badge-info text-xs">Sent</span>
                        @elseif($transfer->status === 'transfer_completed')
                            <span class="badge badge-warning text-xs">Transfer Done</span>
                        @else
                            <span class="badge badge-success text-xs">Paid</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</span>
                        <span class="text-sm text-slate-500">{{ $transfer->expenses->count() }} expenses</span>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <x-action-button type="view" size="sm" :href="route('transfers.show', $transfer)" class="flex-1 justify-center" />
                        <x-action-button type="edit" size="sm" :href="route('transfers.edit', $transfer)" class="flex-1 justify-center" onclick="event.stopPropagation()" />
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <p class="text-slate-500 mb-4">No transfer requests found</p>
                    <a href="{{ route('transfers.create') }}" class="text-orange-600 hover:text-orange-800 font-medium">Create your first transfer</a>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block table-responsive">
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
                        <tr class="cursor-pointer hover:bg-slate-50" wire:key="desktop-transfer-{{ $transfer->id }}" onclick="window.location='{{ route('transfers.show', $transfer) }}'">
                            <td>
                                <a href="{{ route('transfers.show', $transfer) }}" class="text-orange-600 hover:text-orange-800 font-medium">
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
                            <td onclick="event.stopPropagation()">
                                <div class="flex items-center gap-2">
                                    <x-action-button type="view" size="xs" :href="route('transfers.show', $transfer)" />
                                    <x-action-button type="edit" size="xs" :href="route('transfers.edit', $transfer)" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                No transfer requests found. <a href="{{ route('transfers.create') }}" class="text-orange-600 hover:text-orange-800">Create your first transfer</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-slate-200">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</div>
