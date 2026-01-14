<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Reports</h1>
            <p class="text-slate-500">Business performance and financial insights</p>
        </div>
    </div>

    <!-- Date Filter & Export -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="flex items-center justify-between">
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
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export.bookings', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                    class="btn btn-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Bookings
                </a>
                <a href="{{ route('reports.export.financial', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                    class="btn btn-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Financial
                </a>
            </div>
        </div>
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

    <!-- Charts Row -->
    <div class="grid grid-cols-2 gap-6 mb-8">
        <!-- Monthly Bookings Chart -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Bookings by Month</h2>
            </div>
            <div class="p-6">
                <canvas id="monthlyBookingsChart" height="120"></canvas>
            </div>
        </div>

        <!-- Booking Status Pie Chart -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Booking Status</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-6">
                    <div class="w-48 h-48">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    <div class="flex-1 space-y-3">
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
                                <span class="text-slate-700">Running</span>
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
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <!-- Top Destinations Bar Chart -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Top Destinations</h2>
            </div>
            <div class="p-6">
                @if(count($topCountries) > 0)
                    <canvas id="destinationsChart" height="200"></canvas>
                @else
                    <p class="text-slate-500 text-center py-8">No data available</p>
                @endif
            </div>
        </div>

        <!-- Revenue vs Expenses -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Revenue vs Expenses</h2>
            </div>
            <div class="p-6">
                <canvas id="revenueChart" height="200"></canvas>
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

    <!-- Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Bookings Line Chart
            const monthlyData = @json($monthlyBookings);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyValues = months.map((_, i) => monthlyData[i + 1] || 0);

            new Chart(document.getElementById('monthlyBookingsChart'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Bookings',
                        data: monthlyValues,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // Status Pie Chart
            new Chart(document.getElementById('statusPieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Upcoming', 'Running', 'Completed'],
                    datasets: [{
                        data: [{{ $bookingStats['upcoming'] }}, {{ $bookingStats['active'] }}, {{ $bookingStats['completed'] }}],
                        backgroundColor: ['#3b82f6', '#22c55e', '#94a3b8'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Destinations Bar Chart
            @if(count($topCountries) > 0)
            const destinations = @json($topCountries);
            new Chart(document.getElementById('destinationsChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(destinations),
                    datasets: [{
                        label: 'Bookings',
                        data: Object.values(destinations),
                        backgroundColor: ['#f97316', '#fb923c', '#fdba74', '#fed7aa', '#ffedd5'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
            @endif

            // Revenue vs Expenses Chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: ['Revenue', 'Expenses', 'Profit'],
                    datasets: [{
                        data: [{{ $financialStats['total_received'] }}, {{ $financialStats['total_paid'] }}, {{ $financialStats['profit'] }}],
                        backgroundColor: ['#22c55e', '#ef4444', '{{ $financialStats['profit'] >= 0 ? '#8b5cf6' : '#ef4444' }}'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
