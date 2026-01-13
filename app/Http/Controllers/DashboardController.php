<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Task;
use App\Models\Transfer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $upcomingBookings = Booking::with(['groups.travelers'])
            ->where('status', 'upcoming')
            ->where('start_date', '>=', Carbon::today())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        $activeBookings = Booking::with(['groups.travelers'])
            ->where('status', 'active')
            ->orderBy('start_date')
            ->get();

        $pendingTasks = Task::with(['booking', 'assignedTo'])
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $pendingTransfers = Transfer::with(['expenses'])
            ->whereIn('status', ['draft', 'sent'])
            ->orderBy('request_date')
            ->limit(5)
            ->get();

        $stats = [
            'total_bookings' => Booking::count(),
            'upcoming_bookings' => Booking::where('status', 'upcoming')->count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'pending_tasks' => Task::where('status', '!=', 'completed')->count(),
        ];

        return view('dashboard', compact(
            'upcomingBookings',
            'activeBookings',
            'pendingTasks',
            'pendingTransfers',
            'stats'
        ));
    }
}
