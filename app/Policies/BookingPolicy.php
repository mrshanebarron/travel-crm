<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * All authenticated staff members can view bookings.
     * Future: Add role checks for restricted access.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Booking $booking): bool
    {
        return true;
    }

    /**
     * Only allow deletion if booking has no completed payments.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Prevent deletion if there are ledger entries with payments received
        $hasPayments = $booking->ledgerEntries()
            ->where('type', 'received')
            ->exists();

        return !$hasPayments;
    }

    public function restore(User $user, Booking $booking): bool
    {
        return true;
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return false; // Never allow permanent deletion
    }
}
