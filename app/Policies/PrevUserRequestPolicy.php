<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevUserRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevUserRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevUserRequest');
    }

    public function view(AuthUser $authUser, PrevUserRequest $prevUserRequest): bool
    {
        return $authUser->can('View:PrevUserRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevUserRequest');
    }

    public function update(AuthUser $authUser, PrevUserRequest $prevUserRequest): bool
    {
        return $authUser->can('Update:PrevUserRequest');
    }

    public function delete(AuthUser $authUser, PrevUserRequest $prevUserRequest): bool
    {
        return $authUser->can('Delete:PrevUserRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevUserRequest');
    }

    public function restore(AuthUser $authUser, PrevUserRequest $prevUserRequest): bool
    {
        return $authUser->can('Restore:PrevUserRequest');
    }

    public function forceDelete(AuthUser $authUser, PrevUserRequest $prevUserRequest): bool
    {
        return $authUser->can('ForceDelete:PrevUserRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevUserRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevUserRequest');
    }

    public function replicate(AuthUser $authUser, PrevUserRequest $prevUserRequest): bool
    {
        return $authUser->can('Replicate:PrevUserRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevUserRequest');
    }

}