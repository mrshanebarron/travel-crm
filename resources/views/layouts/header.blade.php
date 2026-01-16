<header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3 sm:py-4 sticky top-0 z-30">
    <div class="flex items-center justify-between gap-4">
        <!-- Left section: Hamburger + Search -->
        <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
            <!-- Mobile hamburger menu button -->
            <button
                @click="sidebarOpen = !sidebarOpen"
                :class="sidebarOpen ? 'hamburger-active' : ''"
                class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors flex-shrink-0"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path class="hamburger-line" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16" />
                    <path class="hamburger-line" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16" />
                    <path class="hamburger-line" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18h16" />
                </svg>
            </button>

            <!-- Search form -->
            <form action="{{ route('search') }}" method="GET" class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search..."
                    class="pl-10 pr-4 py-2 w-full border border-slate-200 rounded-lg focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none text-sm sm:text-base"
                />
            </form>
        </div>

        <!-- Right section: Notifications + Settings -->
        <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
            <!-- Notifications Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg relative transition-colors">
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
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    @endif
                </button>
                <div
                    x-show="open"
                    x-cloak
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50"
                    style="max-width: calc(100vw - 2rem);"
                >
                    <div class="px-4 py-3 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h3 class="font-semibold text-slate-900">Notifications</h3>
                    </div>
                    <div class="max-h-80 sm:max-h-96 overflow-y-auto">
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
                            <a href="{{ route('bookings.show', $task->booking) }}" class="block px-4 py-3 hover:bg-orange-50 border-b border-slate-100 transition-colors">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0 animate-pulse"></span>
                                    <p class="text-sm font-medium text-slate-900">Task Overdue</p>
                                </div>
                                <p class="text-sm text-slate-600 mt-1 line-clamp-1">{{ $task->name }}</p>
                                <p class="text-xs text-red-600 mt-1 font-medium">Due {{ $task->due_date->diffForHumans() }}</p>
                            </a>
                        @empty
                        @endforelse

                        @foreach($upcomingBookings as $booking)
                            @php $lead = $booking->travelers->where('is_lead', true)->first(); @endphp
                            <a href="{{ route('bookings.show', $booking) }}" class="block px-4 py-3 hover:bg-orange-50 border-b border-slate-100 transition-colors">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-teal-500 rounded-full flex-shrink-0"></span>
                                    <p class="text-sm font-medium text-slate-900">Upcoming Safari</p>
                                </div>
                                <p class="text-sm text-slate-600 mt-1 line-clamp-1">{{ $booking->booking_number }} - {{ $lead?->full_name ?? 'No lead' }}</p>
                                <p class="text-xs text-teal-600 mt-1 font-medium">Starts {{ $booking->start_date->diffForHumans() }}</p>
                            </a>
                        @endforeach

                        @if($overdueTasks->isEmpty() && $upcomingBookings->isEmpty())
                            <div class="px-4 py-8 text-center">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-slate-700">All caught up!</p>
                                <p class="text-xs text-slate-500 mt-1">No pending notifications</p>
                            </div>
                        @endif
                    </div>
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                        <a href="{{ route('tasks.index') }}?filter=overdue" class="text-sm text-orange-600 hover:text-orange-700 font-medium">View all tasks</a>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <div
                    x-show="open"
                    x-cloak
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50"
                >
                    <div class="px-4 py-3 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <p class="font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-orange-50 transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile Settings
                        </a>
                        <a href="{{ route('tasks.index') }}?filter=mine" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-orange-50 transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            My Tasks
                        </a>
                    </div>
                    <div class="border-t border-slate-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
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
