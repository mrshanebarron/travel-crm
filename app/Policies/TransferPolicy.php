<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;

class TransferPolicy
{
    /**
     * Superadmin and Admin can view transfers (view_transfers permission).
     * Users cannot view transfers at all.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_transfers');
    }

    public function view(User $user, Transfer $transfer): bool
    {
        return $user->can('view_transfers');
    }

    /**
     * Only superadmins can create transfers (manage_transfers permission).
     */
    public function create(User $user): bool
    {
        return $user->can('manage_transfers');
    }

    /**
     * Only superadmins can update transfers. Cannot modify completed transfers.
     */
    public function update(User $user, Transfer $transfer): bool
    {
        if (!$user->can('manage_transfers')) {
            return false;
        }

        // Cannot modify completed transfers
        return $transfer->status !== 'vendor_payments_completed';
    }

    /**
     * Only superadmins can delete, and only draft transfers.
     */
    public function delete(User $user, Transfer $transfer): bool
    {
        if (!$user->can('manage_transfers')) {
            return false;
        }

        return $transfer->status === 'draft';
    }

    /**
     * Only superadmins can restore.
     */
    public function restore(User $user, Transfer $transfer): bool
    {
        return $user->can('manage_transfers');
    }

    /**
     * Never allow permanent deletion.
     */
    public function forceDelete(User $user, Transfer $transfer): bool
    {
        return false;
    }
}
