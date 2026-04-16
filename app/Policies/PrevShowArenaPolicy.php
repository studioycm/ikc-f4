<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShowArena;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowArenaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShowArena');
    }

    public function view(AuthUser $authUser, PrevShowArena $prevShowArena): bool
    {
        return $authUser->can('View:PrevShowArena');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShowArena');
    }

    public function update(AuthUser $authUser, PrevShowArena $prevShowArena): bool
    {
        return $authUser->can('Update:PrevShowArena');
    }

    public function delete(AuthUser $authUser, PrevShowArena $prevShowArena): bool
    {
        return $authUser->can('Delete:PrevShowArena');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShowArena');
    }

    public function restore(AuthUser $authUser, PrevShowArena $prevShowArena): bool
    {
        return $authUser->can('Restore:PrevShowArena');
    }

    public function forceDelete(AuthUser $authUser, PrevShowArena $prevShowArena): bool
    {
        return $authUser->can('ForceDelete:PrevShowArena');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShowArena');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShowArena');
    }

    public function replicate(AuthUser $authUser, PrevShowArena $prevShowArena): bool
    {
        return $authUser->can('Replicate:PrevShowArena');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShowArena');
    }

}