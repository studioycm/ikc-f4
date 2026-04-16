<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevBreeding;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevBreedingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevBreeding');
    }

    public function view(AuthUser $authUser, PrevBreeding $prevBreeding): bool
    {
        return $authUser->can('View:PrevBreeding');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevBreeding');
    }

    public function update(AuthUser $authUser, PrevBreeding $prevBreeding): bool
    {
        return $authUser->can('Update:PrevBreeding');
    }

    public function delete(AuthUser $authUser, PrevBreeding $prevBreeding): bool
    {
        return $authUser->can('Delete:PrevBreeding');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevBreeding');
    }

    public function restore(AuthUser $authUser, PrevBreeding $prevBreeding): bool
    {
        return $authUser->can('Restore:PrevBreeding');
    }

    public function forceDelete(AuthUser $authUser, PrevBreeding $prevBreeding): bool
    {
        return $authUser->can('ForceDelete:PrevBreeding');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevBreeding');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevBreeding');
    }

    public function replicate(AuthUser $authUser, PrevBreeding $prevBreeding): bool
    {
        return $authUser->can('Replicate:PrevBreeding');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevBreeding');
    }

}