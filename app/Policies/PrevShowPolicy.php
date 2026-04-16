<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShow;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShow');
    }

    public function view(AuthUser $authUser, PrevShow $prevShow): bool
    {
        return $authUser->can('View:PrevShow');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShow');
    }

    public function update(AuthUser $authUser, PrevShow $prevShow): bool
    {
        return $authUser->can('Update:PrevShow');
    }

    public function delete(AuthUser $authUser, PrevShow $prevShow): bool
    {
        return $authUser->can('Delete:PrevShow');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShow');
    }

    public function restore(AuthUser $authUser, PrevShow $prevShow): bool
    {
        return $authUser->can('Restore:PrevShow');
    }

    public function forceDelete(AuthUser $authUser, PrevShow $prevShow): bool
    {
        return $authUser->can('ForceDelete:PrevShow');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShow');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShow');
    }

    public function replicate(AuthUser $authUser, PrevShow $prevShow): bool
    {
        return $authUser->can('Replicate:PrevShow');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShow');
    }

}