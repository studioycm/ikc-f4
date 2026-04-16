<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevUserPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevUser');
    }

    public function view(AuthUser $authUser, PrevUser $prevUser): bool
    {
        return $authUser->can('View:PrevUser');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevUser');
    }

    public function update(AuthUser $authUser, PrevUser $prevUser): bool
    {
        return $authUser->can('Update:PrevUser');
    }

    public function delete(AuthUser $authUser, PrevUser $prevUser): bool
    {
        return $authUser->can('Delete:PrevUser');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevUser');
    }

    public function restore(AuthUser $authUser, PrevUser $prevUser): bool
    {
        return $authUser->can('Restore:PrevUser');
    }

    public function forceDelete(AuthUser $authUser, PrevUser $prevUser): bool
    {
        return $authUser->can('ForceDelete:PrevUser');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevUser');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevUser');
    }

    public function replicate(AuthUser $authUser, PrevUser $prevUser): bool
    {
        return $authUser->can('Replicate:PrevUser');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevUser');
    }

}