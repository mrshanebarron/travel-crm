<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-slate-900">Financial Ledger</h2>
    </div>

    <!-- Add Entry Form -->
    <form wire:submit="addEntry" class="mb-6 p-4 bg-slate-50 rounded-xl">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date</label>
                <input type="date" wire:model="date" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Type</label>
                <select wire:model.live="type" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    <option value="received">Received</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            @if($type === 'received')
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payment Type</label>
                    <select wire:model="receivedCategory" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="deposit">Deposit (25%)</option>
                        <option value="90_day">90-Day Payment (25%)</option>
                        <option value="45_day">45-Day Payment (50%)</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            @else
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Expense Category</label>
                    <select wire:model="paidCategory" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="lodges_camps">Lodges/Camps</option>
                        <option value="driver_guide">Driver/Guide</option>
                        <option value="park_entry">Park Entry</option>
                        <option value="arrival_dept_flight">Arrival/Dept Flight</option>
                        <option value="internal_flights">Internal Flights</option>
                        <option value="driver_guide_invoices">Driver/Guide Invoices</option>
                        <option value="misc">Misc</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Vendor Name</label>
                    <input type="text" wire:model="vendorName" placeholder="Vendor name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
            @endif
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Amount</label>
                <input type="number" wire:model="amount" placeholder="0.00" step="0.01" min="0" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes</label>
                <input type="text" wire:model="description" placeholder="Optional notes" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div>
                <x-action-button type="add" label="Add Entry" :submit="true" class="w-full justify-center" />
            </div>
        </div>
    </form>

    <!-- Ledger Summary -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <div class="text-sm text-green-600 font-medium">Total Received</div>
            <div class="text-2xl font-bold text-green-700">${{ number_format($totalReceived, 2) }}</div>
        </div>
        <div class="bg-red-50 rounded-xl p-4 text-center">
            <div class="text-sm text-red-600 font-medium">Total Paid</div>
            <div class="text-2xl font-bold text-red-700">${{ number_format($totalPaid, 2) }}</div>
        </div>
        <div class="{{ $balance >= 0 ? 'bg-purple-50' : 'bg-red-50' }} rounded-xl p-4 text-center">
            <div class="text-sm {{ $balance >= 0 ? 'text-purple-600' : 'text-red-600' }} font-medium">Net Balance</div>
            <div class="text-2xl font-bold {{ $balance >= 0 ? 'text-purple-700' : 'text-red-700' }}">${{ number_format($balance, 2) }}</div>
        </div>
    </div>

    <!-- Ledger Table -->
    @if($entries->count() > 0)
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-right">Received</th>
                        <th class="text-right">Paid</th>
                        <th class="text-right">Balance</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr wire:key="entry-{{ $entry->id }}">
                            <td class="text-slate-900">{{ $entry->date->format('M j, Y') }}</td>
                            <td class="text-slate-900">{{ $entry->description }}</td>
                            <td class="text-right text-green-600 font-medium">
                                {{ $entry->type === 'received' ? '$' . number_format($entry->amount, 2) : '' }}
                            </td>
                            <td class="text-right text-red-600 font-medium">
                                {{ $entry->type === 'paid' ? '$' . number_format($entry->amount, 2) : '' }}
                            </td>
                            <td class="text-right font-semibold {{ $entry->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($entry->balance, 2) }}
                            </td>
                            <td class="text-right">
                                <x-action-button type="delete" size="xs" :icon="false" wire:click="deleteEntry({{ $entry->id }})" wire:confirm="Delete this entry?" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12 text-slate-500">
            <svg class="mx-auto mb-4 text-slate-300" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <p>No ledger entries yet</p>
        </div>
    @endif
</div>
