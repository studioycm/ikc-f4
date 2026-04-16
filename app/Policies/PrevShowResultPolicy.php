<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShowResult;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowResultPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShowResult');
    }

    public function view(AuthUser $authUser, PrevShowResult $prevShowResult): bool
    {
        return $authUser->can('View:PrevShowResult');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShowResult');
    }

    public function update(AuthUser $authUser, PrevShowResult $prevShowResult): bool
    {
        return $authUser->can('Update:PrevShowResult');
    }

    public function delete(AuthUser $authUser, PrevShowResult $prevShowResult): bool
    {
        return $authUser->can('Delete:PrevShowResult');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShowResult');
    }

    public function restore(AuthUser $authUser, PrevShowResult $prevShowResult): bool
    {
        return $authUser->can('Restore:PrevShowResult');
    }

    public function forceDelete(AuthUser $authUser, PrevShowResult $prevShowResult): bool
    {
        return $authUser->can('ForceDelete:PrevShowResult');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShowResult');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShowResult');
    }

    public function replicate(AuthUser $authUser, PrevShowResult $prevShowResult): bool
    {
        return $authUser->can('Replicate:PrevShowResult');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShowResult');
    }

}