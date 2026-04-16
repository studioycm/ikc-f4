<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevTitle;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevTitlePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevTitle');
    }

    public function view(AuthUser $authUser, PrevTitle $prevTitle): bool
    {
        return $authUser->can('View:PrevTitle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevTitle');
    }

    public function update(AuthUser $authUser, PrevTitle $prevTitle): bool
    {
        return $authUser->can('Update:PrevTitle');
    }

    public function delete(AuthUser $authUser, PrevTitle $prevTitle): bool
    {
        return $authUser->can('Delete:PrevTitle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevTitle');
    }

    public function restore(AuthUser $authUser, PrevTitle $prevTitle): bool
    {
        return $authUser->can('Restore:PrevTitle');
    }

    public function forceDelete(AuthUser $authUser, PrevTitle $prevTitle): bool
    {
        return $authUser->can('ForceDelete:PrevTitle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevTitle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevTitle');
    }

    public function replicate(AuthUser $authUser, PrevTitle $prevTitle): bool
    {
        return $authUser->can('Replicate:PrevTitle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevTitle');
    }

}