<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevDog;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevDogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevDog');
    }

    public function view(AuthUser $authUser, PrevDog $prevDog): bool
    {
        return $authUser->can('View:PrevDog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevDog');
    }

    public function update(AuthUser $authUser, PrevDog $prevDog): bool
    {
        return $authUser->can('Update:PrevDog');
    }

    public function delete(AuthUser $authUser, PrevDog $prevDog): bool
    {
        return $authUser->can('Delete:PrevDog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevDog');
    }

    public function restore(AuthUser $authUser, PrevDog $prevDog): bool
    {
        return $authUser->can('Restore:PrevDog');
    }

    public function forceDelete(AuthUser $authUser, PrevDog $prevDog): bool
    {
        return $authUser->can('ForceDelete:PrevDog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevDog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevDog');
    }

    public function replicate(AuthUser $authUser, PrevDog $prevDog): bool
    {
        return $authUser->can('Replicate:PrevDog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevDog');
    }

}