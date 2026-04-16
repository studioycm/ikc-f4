<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevColor;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevColorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevColor');
    }

    public function view(AuthUser $authUser, PrevColor $prevColor): bool
    {
        return $authUser->can('View:PrevColor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevColor');
    }

    public function update(AuthUser $authUser, PrevColor $prevColor): bool
    {
        return $authUser->can('Update:PrevColor');
    }

    public function delete(AuthUser $authUser, PrevColor $prevColor): bool
    {
        return $authUser->can('Delete:PrevColor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevColor');
    }

    public function restore(AuthUser $authUser, PrevColor $prevColor): bool
    {
        return $authUser->can('Restore:PrevColor');
    }

    public function forceDelete(AuthUser $authUser, PrevColor $prevColor): bool
    {
        return $authUser->can('ForceDelete:PrevColor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevColor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevColor');
    }

    public function replicate(AuthUser $authUser, PrevColor $prevColor): bool
    {
        return $authUser->can('Replicate:PrevColor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevColor');
    }

}