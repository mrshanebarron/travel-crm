<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    /**
     * Everyone can view vendors list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Everyone can view a vendor.
     */
    public function view(User $user, Vendor $vendor): bool
    {
        return true;
    }

    /**
     * Only admins and managers can create vendors.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Only admins and managers can update vendors.
     */
    public function update(User $user, Vendor $vendor): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Only admins can delete vendors.
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->role === 'admin';
    }
}
