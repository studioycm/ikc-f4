<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevBreed;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevBreedPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevBreed');
    }

    public function view(AuthUser $authUser, PrevBreed $prevBreed): bool
    {
        return $authUser->can('View:PrevBreed');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevBreed');
    }

    public function update(AuthUser $authUser, PrevBreed $prevBreed): bool
    {
        return $authUser->can('Update:PrevBreed');
    }

    public function delete(AuthUser $authUser, PrevBreed $prevBreed): bool
    {
        return $authUser->can('Delete:PrevBreed');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevBreed');
    }

    public function restore(AuthUser $authUser, PrevBreed $prevBreed): bool
    {
        return $authUser->can('Restore:PrevBreed');
    }

    public function forceDelete(AuthUser $authUser, PrevBreed $prevBreed): bool
    {
        return $authUser->can('ForceDelete:PrevBreed');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevBreed');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevBreed');
    }

    public function replicate(AuthUser $authUser, PrevBreed $prevBreed): bool
    {
        return $authUser->can('Replicate:PrevBreed');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevBreed');
    }

}