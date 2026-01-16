<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Payment Reconciliation</h1>
            <p class="text-slate-500">Track expected vs received payments across all bookings</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('reconciliation.index') }}" class="flex items-center gap-4">
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div class="pt-5">
                <x-action-button type="filter" label="Apply Filter" :submit="true" />
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Expected To Date</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">${{ number_format($summary['total_expected_to_date'], 0) }}</p>
            <p class="text-xs text-slate-400 mt-1">of ${{ number_format($summary['total_expected'], 0) }} total</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Total Received</span>
            </div>
            <p class="text-2xl font-bold text-green-600">${{ number_format($summary['total_received'], 0) }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 {{ $summary['total_variance'] >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $summary['total_variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Variance</span>
            </div>
            <p class="text-2xl font-bold {{ $summary['total_variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $summary['total_variance'] >= 0 ? '+' : '' }}${{ number_format($summary['total_variance'], 0) }}
            </p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Attention Needed</span>
            </div>
            <p class="text-2xl font-bold text-orange-600">{{ $summary['overdue_count'] + $summary['pending_count'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $summary['overdue_count'] }} overdue, {{ $summary['pending_count'] }} pending</p>
        </div>
    </div>

    <!-- Payment Status Legend -->
    <div class="flex items-center gap-6 mb-4 text-sm">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500"></span>
            <span class="text-slate-600">Paid in Full ({{ $reconciliationData->where('status', 'paid')->count() }})</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            <span class="text-slate-600">Current ({{ $summary['current_count'] }})</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
            <span class="text-slate-600">Pending ({{ $summary['pending_count'] }})</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            <span class="text-slate-600">Overdue ({{ $summary['overdue_count'] }})</span>
        </div>
    </div>

    <!-- Reconciliation Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Trip Date</th>
                    <th>Status</th>
                    <th class="text-right">Expected</th>
                    <th class="text-right">Received</th>
                    <th class="text-right">Variance</th>
                    <th class="text-right">Vendor Paid</th>
                    <th class="text-right">Profit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reconciliationData as $data)
                    <tr class="cursor-pointer hover:bg-slate-50" onclick="window.location='{{ route('bookings.show', $data['booking']) }}'">
                        <td>
                            <div class="font-medium text-slate-900">{{ $data['booking']->booking_number }}</div>
                            <div class="text-sm text-slate-500">
                                {{ $data['lead_traveler']?->full_name ?? 'No lead' }}
                                @if($data['traveler_count'] > 1)
                                    +{{ $data['traveler_count'] - 1 }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-slate-900">{{ $data['booking']->start_date->format('M j, Y') }}</div>
                            <div class="text-xs text-slate-500">
                                @if($data['days_until_trip'] > 0)
                                    {{ $data['days_until_trip'] }} days away
                                @elseif($data['days_until_trip'] == 0)
                                    Today
                                @else
                                    {{ abs($data['days_until_trip']) }} days ago
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($data['status'] === 'paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($data['status'] === 'current')
                                <span class="badge badge-info">Current</span>
                            @elseif($data['status'] === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge" style="background: #fef2f2; color: #dc2626;">Overdue</span>
                            @endif
                            <div class="flex gap-1 mt-1">
                                <span class="w-2 h-2 rounded-full {{ $data['deposit_due'] && $data['total_received'] >= ($data['total_expected'] * 0.25) ? 'bg-green-400' : 'bg-slate-200' }}" title="Deposit"></span>
                                <span class="w-2 h-2 rounded-full {{ $data['second_payment_due'] ? ($data['total_received'] >= ($data['total_expected'] * 0.5) ? 'bg-green-400' : 'bg-yellow-400') : 'bg-slate-200' }}" title="Second Payment"></span>
                                <span class="w-2 h-2 rounded-full {{ $data['final_payment_due'] ? ($data['total_received'] >= $data['total_expected'] ? 'bg-green-400' : 'bg-yellow-400') : 'bg-slate-200' }}" title="Final Payment"></span>
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="font-semibold text-slate-900">${{ number_format($data['expected_to_date'], 0) }}</div>
                            <div class="text-xs text-slate-400">${{ number_format($data['total_expected'], 0) }} total</div>
                        </td>
                        <td class="text-right font-semibold {{ $data['total_received'] >= $data['expected_to_date'] ? 'text-green-600' : 'text-slate-900' }}">
                            ${{ number_format($data['total_received'], 0) }}
                        </td>
                        <td class="text-right font-semibold {{ $data['variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['variance'] >= 0 ? '+' : '' }}${{ number_format($data['variance'], 0) }}
                        </td>
                        <td class="text-right text-slate-600">
                            ${{ number_format($data['total_paid'], 0) }}
                        </td>
                        <td class="text-right font-semibold {{ $data['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($data['profit'], 0) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            No bookings in the selected date range
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($reconciliationData->count() > 0)
                <tfoot class="bg-slate-50 border-t border-slate-200">
                    <tr>
                        <td colspan="3" class="font-semibold text-slate-700">Totals</td>
                        <td class="text-right font-bold text-slate-900">${{ number_format($summary['total_expected_to_date'], 0) }}</td>
                        <td class="text-right font-bold text-green-600">${{ number_format($summary['total_received'], 0) }}</td>
                        <td class="text-right font-bold {{ $summary['total_variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $summary['total_variance'] >= 0 ? '+' : '' }}${{ number_format($summary['total_variance'], 0) }}
                        </td>
                        <td class="text-right font-bold text-slate-600">${{ number_format($summary['total_paid'], 0) }}</td>
                        <td class="text-right font-bold {{ $summary['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($summary['total_profit'], 0) }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <!-- Payment Schedule Key -->
    <div class="mt-6 bg-slate-50 rounded-xl p-4 text-sm text-slate-600">
        <p class="font-medium text-slate-700 mb-2">Payment Schedule Key</p>
        <div class="flex gap-8">
            <div><span class="font-medium">Deposit (25%):</span> Due upon booking</div>
            <div><span class="font-medium">Second Payment (25%):</span> Due 90 days before trip</div>
            <div><span class="font-medium">Final Payment (50%):</span> Due 45 days before trip</div>
        </div>
    </div>
</x-app-layout>
