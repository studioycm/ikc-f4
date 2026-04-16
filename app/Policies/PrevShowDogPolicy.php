<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShowDog;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowDogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShowDog');
    }

    public function view(AuthUser $authUser, PrevShowDog $prevShowDog): bool
    {
        return $authUser->can('View:PrevShowDog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShowDog');
    }

    public function update(AuthUser $authUser, PrevShowDog $prevShowDog): bool
    {
        return $authUser->can('Update:PrevShowDog');
    }

    public function delete(AuthUser $authUser, PrevShowDog $prevShowDog): bool
    {
        return $authUser->can('Delete:PrevShowDog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShowDog');
    }

    public function restore(AuthUser $authUser, PrevShowDog $prevShowDog): bool
    {
        return $authUser->can('Restore:PrevShowDog');
    }

    public function forceDelete(AuthUser $authUser, PrevShowDog $prevShowDog): bool
    {
        return $authUser->can('ForceDelete:PrevShowDog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShowDog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShowDog');
    }

    public function replicate(AuthUser $authUser, PrevShowDog $prevShowDog): bool
    {
        return $authUser->can('Replicate:PrevShowDog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShowDog');
    }

}