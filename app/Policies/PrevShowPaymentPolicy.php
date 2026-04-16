<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShowPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowPaymentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShowPayment');
    }

    public function view(AuthUser $authUser, PrevShowPayment $prevShowPayment): bool
    {
        return $authUser->can('View:PrevShowPayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShowPayment');
    }

    public function update(AuthUser $authUser, PrevShowPayment $prevShowPayment): bool
    {
        return $authUser->can('Update:PrevShowPayment');
    }

    public function delete(AuthUser $authUser, PrevShowPayment $prevShowPayment): bool
    {
        return $authUser->can('Delete:PrevShowPayment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShowPayment');
    }

    public function restore(AuthUser $authUser, PrevShowPayment $prevShowPayment): bool
    {
        return $authUser->can('Restore:PrevShowPayment');
    }

    public function forceDelete(AuthUser $authUser, PrevShowPayment $prevShowPayment): bool
    {
        return $authUser->can('ForceDelete:PrevShowPayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShowPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShowPayment');
    }

    public function replicate(AuthUser $authUser, PrevShowPayment $prevShowPayment): bool
    {
        return $authUser->can('Replicate:PrevShowPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShowPayment');
    }

}