<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;

class TransferPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transfer $transfer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transfer $transfer): bool
    {
        // Cannot modify completed transfers
        return $transfer->status !== 'vendor_payments_completed';
    }

    /**
     * Only allow deletion of draft transfers.
     */
    public function delete(User $user, Transfer $transfer): bool
    {
        return $transfer->status === 'draft';
    }

    public function restore(User $user, Transfer $transfer): bool
    {
        return true;
    }

    public function forceDelete(User $user, Transfer $transfer): bool
    {
        return false;
    }
}
