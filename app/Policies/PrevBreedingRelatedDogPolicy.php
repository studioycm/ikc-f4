<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevBreedingRelatedDog;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevBreedingRelatedDogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevBreedingRelatedDog');
    }

    public function view(AuthUser $authUser, PrevBreedingRelatedDog $prevBreedingRelatedDog): bool
    {
        return $authUser->can('View:PrevBreedingRelatedDog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevBreedingRelatedDog');
    }

    public function update(AuthUser $authUser, PrevBreedingRelatedDog $prevBreedingRelatedDog): bool
    {
        return $authUser->can('Update:PrevBreedingRelatedDog');
    }

    public function delete(AuthUser $authUser, PrevBreedingRelatedDog $prevBreedingRelatedDog): bool
    {
        return $authUser->can('Delete:PrevBreedingRelatedDog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevBreedingRelatedDog');
    }

    public function restore(AuthUser $authUser, PrevBreedingRelatedDog $prevBreedingRelatedDog): bool
    {
        return $authUser->can('Restore:PrevBreedingRelatedDog');
    }

    public function forceDelete(AuthUser $authUser, PrevBreedingRelatedDog $prevBreedingRelatedDog): bool
    {
        return $authUser->can('ForceDelete:PrevBreedingRelatedDog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevBreedingRelatedDog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevBreedingRelatedDog');
    }

    public function replicate(AuthUser $authUser, PrevBreedingRelatedDog $prevBreedingRelatedDog): bool
    {
        return $authUser->can('Replicate:PrevBreedingRelatedDog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevBreedingRelatedDog');
    }

}