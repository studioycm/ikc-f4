<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevVetAuth;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevVetAuthPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevVetAuth');
    }

    public function view(AuthUser $authUser, PrevVetAuth $prevVetAuth): bool
    {
        return $authUser->can('View:PrevVetAuth');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevVetAuth');
    }

    public function update(AuthUser $authUser, PrevVetAuth $prevVetAuth): bool
    {
        return $authUser->can('Update:PrevVetAuth');
    }

    public function delete(AuthUser $authUser, PrevVetAuth $prevVetAuth): bool
    {
        return $authUser->can('Delete:PrevVetAuth');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevVetAuth');
    }

    public function restore(AuthUser $authUser, PrevVetAuth $prevVetAuth): bool
    {
        return $authUser->can('Restore:PrevVetAuth');
    }

    public function forceDelete(AuthUser $authUser, PrevVetAuth $prevVetAuth): bool
    {
        return $authUser->can('ForceDelete:PrevVetAuth');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevVetAuth');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevVetAuth');
    }

    public function replicate(AuthUser $authUser, PrevVetAuth $prevVetAuth): bool
    {
        return $authUser->can('Replicate:PrevVetAuth');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevVetAuth');
    }

}