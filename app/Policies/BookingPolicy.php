<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * All authenticated staff members can view bookings.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->isStaff();
    }

    /**
     * Managers and admins can create bookings.
     */
    public function create(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Staff can update bookings they created, managers/admins can update any.
     */
    public function update(User $user, Booking $booking): bool
    {
        if ($user->isManager()) {
            return true;
        }

        // Staff can only update bookings they created
        return $booking->created_by === $user->id;
    }

    /**
     * Only managers/admins can delete, and only if no payments received.
     */
    public function delete(User $user, Booking $booking): bool
    {
        if (!$user->isManager()) {
            return false;
        }

        // Prevent deletion if there are ledger entries with payments received
        $hasPayments = $booking->ledgerEntries()
            ->where('type', 'received')
            ->exists();

        return !$hasPayments;
    }

    /**
     * Only admins can restore deleted bookings.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    /**
     * Never allow permanent deletion.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}
