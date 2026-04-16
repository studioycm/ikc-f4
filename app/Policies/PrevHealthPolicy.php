<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevHealth;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevHealthPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevHealth');
    }

    public function view(AuthUser $authUser, PrevHealth $prevHealth): bool
    {
        return $authUser->can('View:PrevHealth');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevHealth');
    }

    public function update(AuthUser $authUser, PrevHealth $prevHealth): bool
    {
        return $authUser->can('Update:PrevHealth');
    }

    public function delete(AuthUser $authUser, PrevHealth $prevHealth): bool
    {
        return $authUser->can('Delete:PrevHealth');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevHealth');
    }

    public function restore(AuthUser $authUser, PrevHealth $prevHealth): bool
    {
        return $authUser->can('Restore:PrevHealth');
    }

    public function forceDelete(AuthUser $authUser, PrevHealth $prevHealth): bool
    {
        return $authUser->can('ForceDelete:PrevHealth');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevHealth');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevHealth');
    }

    public function replicate(AuthUser $authUser, PrevHealth $prevHealth): bool
    {
        return $authUser->can('Replicate:PrevHealth');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevHealth');
    }

}