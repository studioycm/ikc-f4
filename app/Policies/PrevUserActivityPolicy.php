<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevUserActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevUserActivityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevUserActivity');
    }

    public function view(AuthUser $authUser, PrevUserActivity $prevUserActivity): bool
    {
        return $authUser->can('View:PrevUserActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevUserActivity');
    }

    public function update(AuthUser $authUser, PrevUserActivity $prevUserActivity): bool
    {
        return $authUser->can('Update:PrevUserActivity');
    }

    public function delete(AuthUser $authUser, PrevUserActivity $prevUserActivity): bool
    {
        return $authUser->can('Delete:PrevUserActivity');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevUserActivity');
    }

    public function restore(AuthUser $authUser, PrevUserActivity $prevUserActivity): bool
    {
        return $authUser->can('Restore:PrevUserActivity');
    }

    public function forceDelete(AuthUser $authUser, PrevUserActivity $prevUserActivity): bool
    {
        return $authUser->can('ForceDelete:PrevUserActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevUserActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevUserActivity');
    }

    public function replicate(AuthUser $authUser, PrevUserActivity $prevUserActivity): bool
    {
        return $authUser->can('Replicate:PrevUserActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevUserActivity');
    }

}