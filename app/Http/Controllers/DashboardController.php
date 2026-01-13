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
            ->limit(10)
            ->get();

        $activeBookings = Booking::with(['groups.travelers'])
            ->where('status', 'active')
            ->orderBy('start_date')
            ->get();

        $completedBookings = Booking::where('status', 'completed')->count();

        $tasksAssignedToMe = Task::where('assigned_to', auth()->id())
            ->where('status', '!=', 'completed')
            ->count();

        $tasksAssignedByMe = Task::where('assigned_by', auth()->id())
            ->where('assigned_to', '!=', auth()->id())
            ->where('status', '!=', 'completed')
            ->count();

        $stats = [
            'upcoming_bookings' => Booking::where('status', 'upcoming')->count(),
            'active_bookings' => Booking::where('status', 'active')->count(),
            'completed_bookings' => $completedBookings,
            'tasks_assigned_to_me' => $tasksAssignedToMe,
            'tasks_assigned_by_me' => $tasksAssignedByMe,
        ];

        return view('dashboard', compact(
            'upcomingBookings',
            'activeBookings',
            'stats'
        ));
    }
}
