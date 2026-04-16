<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShowClass;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowClassPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShowClass');
    }

    public function view(AuthUser $authUser, PrevShowClass $prevShowClass): bool
    {
        return $authUser->can('View:PrevShowClass');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShowClass');
    }

    public function update(AuthUser $authUser, PrevShowClass $prevShowClass): bool
    {
        return $authUser->can('Update:PrevShowClass');
    }

    public function delete(AuthUser $authUser, PrevShowClass $prevShowClass): bool
    {
        return $authUser->can('Delete:PrevShowClass');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShowClass');
    }

    public function restore(AuthUser $authUser, PrevShowClass $prevShowClass): bool
    {
        return $authUser->can('Restore:PrevShowClass');
    }

    public function forceDelete(AuthUser $authUser, PrevShowClass $prevShowClass): bool
    {
        return $authUser->can('ForceDelete:PrevShowClass');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShowClass');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShowClass');
    }

    public function replicate(AuthUser $authUser, PrevShowClass $prevShowClass): bool
    {
        return $authUser->can('Replicate:PrevShowClass');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShowClass');
    }

}