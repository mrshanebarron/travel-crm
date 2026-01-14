<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;

class TransferPolicy
{
    /**
     * All staff can view transfers.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Transfer $transfer): bool
    {
        return $user->isStaff();
    }

    /**
     * Only managers/admins can create transfers (financial).
     */
    public function create(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Managers/admins can update, staff cannot. Cannot modify completed transfers.
     */
    public function update(User $user, Transfer $transfer): bool
    {
        if (!$user->isManager()) {
            return false;
        }

        // Cannot modify completed transfers
        return $transfer->status !== 'vendor_payments_completed';
    }

    /**
     * Only managers/admins can delete, and only draft transfers.
     */
    public function delete(User $user, Transfer $transfer): bool
    {
        if (!$user->isManager()) {
            return false;
        }

        return $transfer->status === 'draft';
    }

    /**
     * Only admins can restore.
     */
    public function restore(User $user, Transfer $transfer): bool
    {
        return $user->isAdmin();
    }

    /**
     * Never allow permanent deletion.
     */
    public function forceDelete(User $user, Transfer $transfer): bool
    {
        return false;
    }
}
