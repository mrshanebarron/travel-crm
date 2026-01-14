<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Reports</h1>
            <p class="text-slate-500">Business performance and financial insights</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-4">
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
                <button type="submit" class="btn btn-primary">Apply Filter</button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Total Bookings</span>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ $bookingStats['total'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Total Travelers</span>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ $travelerStats['total'] }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Revenue</span>
            </div>
            <p class="text-3xl font-bold text-green-600">${{ number_format($financialStats['total_received'], 0) }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Profit</span>
            </div>
            <p class="text-3xl font-bold {{ $financialStats['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ${{ number_format($financialStats['profit'], 0) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <!-- Booking Status Breakdown -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Booking Status</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-slate-700">Upcoming</span>
                        </div>
                        <span class="font-semibold text-slate-900">{{ $bookingStats['upcoming'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-slate-700">Currently Running</span>
                        </div>
                        <span class="font-semibold text-slate-900">{{ $bookingStats['active'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                            <span class="text-slate-700">Completed</span>
                        </div>
                        <span class="font-semibold text-slate-900">{{ $bookingStats['completed'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Destinations -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Top Destinations</h2>
            </div>
            <div class="p-6">
                @if(count($topCountries) > 0)
                    <div class="space-y-4">
                        @foreach($topCountries as $country => $count)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-slate-700">{{ $country }}</span>
                                </div>
                                <span class="font-semibold text-slate-900">{{ $count }} bookings</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500 text-center py-4">No data available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900">Financial Summary</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-3 gap-6">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-sm font-medium text-green-600 mb-1">Total Received</p>
                    <p class="text-2xl font-bold text-green-700">${{ number_format($financialStats['total_received'], 2) }}</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-sm font-medium text-red-600 mb-1">Total Paid</p>
                    <p class="text-2xl font-bold text-red-700">${{ number_format($financialStats['total_paid'], 2) }}</p>
                </div>
                <div class="text-center p-4 {{ $financialStats['profit'] >= 0 ? 'bg-purple-50' : 'bg-red-50' }} rounded-lg">
                    <p class="text-sm font-medium {{ $financialStats['profit'] >= 0 ? 'text-purple-600' : 'text-red-600' }} mb-1">Net Profit</p>
                    <p class="text-2xl font-bold {{ $financialStats['profit'] >= 0 ? 'text-purple-700' : 'text-red-700' }}">${{ number_format($financialStats['profit'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transfers -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Recent Transfers</h2>
            <a href="{{ route('transfers.index') }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium">View All</a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Transfer #</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        <td>
                            <a href="{{ route('transfers.show', $transfer) }}" class="text-orange-600 hover:text-orange-800 font-medium">
                                {{ $transfer->transfer_number }}
                            </a>
                        </td>
                        <td class="text-slate-600">{{ $transfer->request_date->format('M j, Y') }}</td>
                        <td>
                            @if($transfer->status === 'draft')
                                <span class="badge" style="background: #f1f5f9; color: #475569;">Draft</span>
                            @elseif($transfer->status === 'sent')
                                <span class="badge badge-info">Sent</span>
                            @elseif($transfer->status === 'transfer_completed')
                                <span class="badge badge-warning">Transfer Done</span>
                            @elseif($transfer->status === 'vendor_payments_completed')
                                <span class="badge badge-success">Completed</span>
                            @endif
                        </td>
                        <td class="font-semibold text-slate-900">${{ number_format($transfer->total_amount, 2) }}</td>
                        <td class="text-slate-600">{{ $transfer->creator->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">No transfers in this period</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
