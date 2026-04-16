<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevHair;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevHairPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevHair');
    }

    public function view(AuthUser $authUser, PrevHair $prevHair): bool
    {
        return $authUser->can('View:PrevHair');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevHair');
    }

    public function update(AuthUser $authUser, PrevHair $prevHair): bool
    {
        return $authUser->can('Update:PrevHair');
    }

    public function delete(AuthUser $authUser, PrevHair $prevHair): bool
    {
        return $authUser->can('Delete:PrevHair');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevHair');
    }

    public function restore(AuthUser $authUser, PrevHair $prevHair): bool
    {
        return $authUser->can('Restore:PrevHair');
    }

    public function forceDelete(AuthUser $authUser, PrevHair $prevHair): bool
    {
        return $authUser->can('ForceDelete:PrevHair');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevHair');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevHair');
    }

    public function replicate(AuthUser $authUser, PrevHair $prevHair): bool
    {
        return $authUser->can('Replicate:PrevHair');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevHair');
    }

}