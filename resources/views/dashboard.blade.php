<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Bookings</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Upcoming</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['upcoming_bookings'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Active</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active_bookings'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Pending Tasks</div>
                    <div class="text-3xl font-bold text-amber-600">{{ $stats['pending_tasks'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Upcoming Bookings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Upcoming Safaris</h3>
                            <a href="{{ route('bookings.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">+ New Booking</a>
                        </div>
                        @forelse($upcomingBookings as $booking)
                            <a href="{{ route('bookings.show', $booking) }}" class="block p-4 border rounded-lg mb-3 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $booking->booking_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->country }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $booking->travelers->count() }} travelers
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-indigo-600">
                                            {{ $booking->start_date->format('M j, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $booking->start_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-gray-500 text-center py-8">No upcoming safaris</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Pending Tasks</h3>
                            <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                        </div>
                        @forelse($pendingTasks as $task)
                            <div class="p-4 border rounded-lg mb-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $task->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $task->booking->booking_number }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($task->due_date)
                                            <div class="text-sm {{ $task->due_date->isPast() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                                {{ $task->due_date->format('M j') }}
                                            </div>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ str_replace('_', ' ', $task->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-500 text-center py-8">No pending tasks</div>
                        @endforelse
                    </div>
                </div>

                <!-- Active Bookings -->
                @if($activeBookings->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Active Safaris</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($activeBookings as $booking)
                                <a href="{{ route('bookings.show', $booking) }}" class="block p-4 border-2 border-green-200 bg-green-50 rounded-lg hover:bg-green-100">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $booking->booking_number }}</div>
                                            <div class="text-sm text-gray-600">{{ $booking->country }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->travelers->count() }} travelers</div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Day {{ now()->diffInDays($booking->start_date) + 1 }} of {{ $booking->start_date->diffInDays($booking->end_date) + 1 }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Pending Transfers -->
                @if($pendingTransfers->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Pending Transfers</h3>
                            <a href="{{ route('transfers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Transfer #</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($pendingTransfers as $transfer)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('transfers.show', $transfer) }}" class="text-indigo-600 hover:text-indigo-800">
                                                    {{ $transfer->transfer_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $transfer->request_date->format('M j, Y') }}</td>
                                            <td class="px-4 py-3 text-sm font-medium">${{ number_format($transfer->total_amount, 2) }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transfer->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ str_replace('_', ' ', $transfer->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
