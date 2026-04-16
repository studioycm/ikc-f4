<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShowBreed;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowBreedPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShowBreed');
    }

    public function view(AuthUser $authUser, PrevShowBreed $prevShowBreed): bool
    {
        return $authUser->can('View:PrevShowBreed');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShowBreed');
    }

    public function update(AuthUser $authUser, PrevShowBreed $prevShowBreed): bool
    {
        return $authUser->can('Update:PrevShowBreed');
    }

    public function delete(AuthUser $authUser, PrevShowBreed $prevShowBreed): bool
    {
        return $authUser->can('Delete:PrevShowBreed');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShowBreed');
    }

    public function restore(AuthUser $authUser, PrevShowBreed $prevShowBreed): bool
    {
        return $authUser->can('Restore:PrevShowBreed');
    }

    public function forceDelete(AuthUser $authUser, PrevShowBreed $prevShowBreed): bool
    {
        return $authUser->can('ForceDelete:PrevShowBreed');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShowBreed');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShowBreed');
    }

    public function replicate(AuthUser $authUser, PrevShowBreed $prevShowBreed): bool
    {
        return $authUser->can('Replicate:PrevShowBreed');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShowBreed');
    }

}