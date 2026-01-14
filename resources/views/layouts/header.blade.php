<header class="bg-white border-b border-slate-200 px-8 py-4 sticky top-0 z-10">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search bookings, travelers..."
                    class="pl-10 pr-4 py-2 w-80 border border-slate-200 rounded-lg focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none"
                />
            </form>
        </div>
        <div class="flex items-center gap-4">
            <!-- Notifications Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @php
                        $overdueTaskCount = \App\Models\Task::where('assigned_to', auth()->id())
                            ->where('status', '!=', 'completed')
                            ->where('due_date', '<', now())
                            ->count();
                    @endphp
                    @if($overdueTaskCount > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-slate-200 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                        <h3 class="font-semibold text-slate-900">Notifications</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @php
                            $overdueTasks = \App\Models\Task::with('booking')
                                ->where('assigned_to', auth()->id())
                                ->where('status', '!=', 'completed')
                                ->where('due_date', '<', now())
                                ->orderBy('due_date')
                                ->limit(5)
                                ->get();

                            $upcomingBookings = \App\Models\Booking::with(['groups.travelers'])
                                ->where('status', 'upcoming')
                                ->where('start_date', '<=', now()->addDays(7))
                                ->orderBy('start_date')
                                ->limit(3)
                                ->get();
                        @endphp

                        @forelse($overdueTasks as $task)
                            <a href="{{ route('bookings.show', $task->booking) }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></span>
                                    <p class="text-sm font-medium text-slate-900">Task Overdue</p>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ $task->name }}</p>
                                <p class="text-xs text-red-600 mt-1">Due {{ $task->due_date->diffForHumans() }}</p>
                            </a>
                        @empty
                        @endforelse

                        @foreach($upcomingBookings as $booking)
                            @php $lead = $booking->travelers->where('is_lead', true)->first(); @endphp
                            <a href="{{ route('bookings.show', $booking) }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-teal-500 rounded-full flex-shrink-0"></span>
                                    <p class="text-sm font-medium text-slate-900">Upcoming Safari</p>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ $booking->booking_number }} - {{ $lead?->full_name ?? 'No lead' }}</p>
                                <p class="text-xs text-teal-600 mt-1">Starts {{ $booking->start_date->diffForHumans() }}</p>
                            </a>
                        @endforeach

                        @if($overdueTasks->isEmpty() && $upcomingBookings->isEmpty())
                            <div class="px-4 py-8 text-center">
                                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-slate-500">All caught up!</p>
                            </div>
                        @endif
                    </div>
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        <a href="{{ route('tasks.index') }}?filter=overdue" class="text-sm text-teal-600 hover:text-teal-700 font-medium">View overdue tasks</a>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-slate-200 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                        <p class="font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile Settings
                        </a>
                        <a href="{{ route('tasks.index') }}?filter=mine" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            My Tasks
                        </a>
                    </div>
                    <div class="border-t border-slate-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
