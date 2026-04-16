<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevClub;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevClubPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevClub');
    }

    public function view(AuthUser $authUser, PrevClub $prevClub): bool
    {
        return $authUser->can('View:PrevClub');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevClub');
    }

    public function update(AuthUser $authUser, PrevClub $prevClub): bool
    {
        return $authUser->can('Update:PrevClub');
    }

    public function delete(AuthUser $authUser, PrevClub $prevClub): bool
    {
        return $authUser->can('Delete:PrevClub');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevClub');
    }

    public function restore(AuthUser $authUser, PrevClub $prevClub): bool
    {
        return $authUser->can('Restore:PrevClub');
    }

    public function forceDelete(AuthUser $authUser, PrevClub $prevClub): bool
    {
        return $authUser->can('ForceDelete:PrevClub');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevClub');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevClub');
    }

    public function replicate(AuthUser $authUser, PrevClub $prevClub): bool
    {
        return $authUser->can('Replicate:PrevClub');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevClub');
    }

}