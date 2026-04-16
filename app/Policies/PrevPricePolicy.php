<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevPrice;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevPricePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevPrice');
    }

    public function view(AuthUser $authUser, PrevPrice $prevPrice): bool
    {
        return $authUser->can('View:PrevPrice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevPrice');
    }

    public function update(AuthUser $authUser, PrevPrice $prevPrice): bool
    {
        return $authUser->can('Update:PrevPrice');
    }

    public function delete(AuthUser $authUser, PrevPrice $prevPrice): bool
    {
        return $authUser->can('Delete:PrevPrice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevPrice');
    }

    public function restore(AuthUser $authUser, PrevPrice $prevPrice): bool
    {
        return $authUser->can('Restore:PrevPrice');
    }

    public function forceDelete(AuthUser $authUser, PrevPrice $prevPrice): bool
    {
        return $authUser->can('ForceDelete:PrevPrice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevPrice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevPrice');
    }

    public function replicate(AuthUser $authUser, PrevPrice $prevPrice): bool
    {
        return $authUser->can('Replicate:PrevPrice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevPrice');
    }

}