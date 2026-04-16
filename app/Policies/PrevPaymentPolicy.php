<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevPaymentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevPayment');
    }

    public function view(AuthUser $authUser, PrevPayment $prevPayment): bool
    {
        return $authUser->can('View:PrevPayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevPayment');
    }

    public function update(AuthUser $authUser, PrevPayment $prevPayment): bool
    {
        return $authUser->can('Update:PrevPayment');
    }

    public function delete(AuthUser $authUser, PrevPayment $prevPayment): bool
    {
        return $authUser->can('Delete:PrevPayment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevPayment');
    }

    public function restore(AuthUser $authUser, PrevPayment $prevPayment): bool
    {
        return $authUser->can('Restore:PrevPayment');
    }

    public function forceDelete(AuthUser $authUser, PrevPayment $prevPayment): bool
    {
        return $authUser->can('ForceDelete:PrevPayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevPayment');
    }

    public function replicate(AuthUser $authUser, PrevPayment $prevPayment): bool
    {
        return $authUser->can('Replicate:PrevPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevPayment');
    }

}