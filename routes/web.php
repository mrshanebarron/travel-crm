<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TravelerController;
use App\Http\Controllers\SafariDayController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransferExpenseController;
use App\Http\Controllers\LedgerEntryController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/import-pdf', [BookingController::class, 'importPdf'])->name('bookings.import-pdf');

    // Groups
    Route::post('/bookings/{booking}/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    // Travelers (nested under groups for creation)
    Route::post('/groups/{group}/travelers', [TravelerController::class, 'store'])->name('travelers.store');
    Route::patch('/travelers/{traveler}', [TravelerController::class, 'update'])->name('travelers.update');
    Route::delete('/travelers/{traveler}', [TravelerController::class, 'destroy'])->name('travelers.destroy');

    // Safari Days
    Route::patch('/safari-days/{safariDay}', [SafariDayController::class, 'update'])->name('safari-days.update');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/bookings/{booking}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Flights
    Route::post('/travelers/{traveler}/flights', [FlightController::class, 'store'])->name('flights.store');
    Route::patch('/flights/{flight}', [FlightController::class, 'update'])->name('flights.update');
    Route::delete('/flights/{flight}', [FlightController::class, 'destroy'])->name('flights.destroy');

    // Documents
    Route::post('/bookings/{booking}/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Rooms
    Route::post('/bookings/{booking}/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::patch('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // Payments
    Route::post('/travelers/{traveler}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::patch('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');

    // Transfers
    Route::resource('transfers', TransferController::class);
    Route::post('/transfers/{transfer}/expenses', [TransferExpenseController::class, 'store'])->name('transfer-expenses.store');
    Route::patch('/transfer-expenses/{transferExpense}', [TransferExpenseController::class, 'update'])->name('transfer-expenses.update');
    Route::delete('/transfer-expenses/{transferExpense}', [TransferExpenseController::class, 'destroy'])->name('transfer-expenses.destroy');

    // Ledger Entries
    Route::post('/bookings/{booking}/ledger-entries', [LedgerEntryController::class, 'store'])->name('ledger-entries.store');
    Route::delete('/ledger-entries/{ledgerEntry}', [LedgerEntryController::class, 'destroy'])->name('ledger-entries.destroy');

    // Activity Logs
    Route::post('/bookings/{booking}/activity-logs', [ActivityLogController::class, 'store'])->name('activity-logs.store');
    Route::delete('/activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
