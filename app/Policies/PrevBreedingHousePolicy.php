<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevBreedingHouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevBreedingHousePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevBreedingHouse');
    }

    public function view(AuthUser $authUser, PrevBreedingHouse $prevBreedingHouse): bool
    {
        return $authUser->can('View:PrevBreedingHouse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevBreedingHouse');
    }

    public function update(AuthUser $authUser, PrevBreedingHouse $prevBreedingHouse): bool
    {
        return $authUser->can('Update:PrevBreedingHouse');
    }

    public function delete(AuthUser $authUser, PrevBreedingHouse $prevBreedingHouse): bool
    {
        return $authUser->can('Delete:PrevBreedingHouse');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevBreedingHouse');
    }

    public function restore(AuthUser $authUser, PrevBreedingHouse $prevBreedingHouse): bool
    {
        return $authUser->can('Restore:PrevBreedingHouse');
    }

    public function forceDelete(AuthUser $authUser, PrevBreedingHouse $prevBreedingHouse): bool
    {
        return $authUser->can('ForceDelete:PrevBreedingHouse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevBreedingHouse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevBreedingHouse');
    }

    public function replicate(AuthUser $authUser, PrevBreedingHouse $prevBreedingHouse): bool
    {
        return $authUser->can('Replicate:PrevBreedingHouse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevBreedingHouse');
    }

}