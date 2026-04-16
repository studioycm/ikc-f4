<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevUserTask;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevUserTaskPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevUserTask');
    }

    public function view(AuthUser $authUser, PrevUserTask $prevUserTask): bool
    {
        return $authUser->can('View:PrevUserTask');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevUserTask');
    }

    public function update(AuthUser $authUser, PrevUserTask $prevUserTask): bool
    {
        return $authUser->can('Update:PrevUserTask');
    }

    public function delete(AuthUser $authUser, PrevUserTask $prevUserTask): bool
    {
        return $authUser->can('Delete:PrevUserTask');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevUserTask');
    }

    public function restore(AuthUser $authUser, PrevUserTask $prevUserTask): bool
    {
        return $authUser->can('Restore:PrevUserTask');
    }

    public function forceDelete(AuthUser $authUser, PrevUserTask $prevUserTask): bool
    {
        return $authUser->can('ForceDelete:PrevUserTask');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevUserTask');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevUserTask');
    }

    public function replicate(AuthUser $authUser, PrevUserTask $prevUserTask): bool
    {
        return $authUser->can('Replicate:PrevUserTask');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevUserTask');
    }

}