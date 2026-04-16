<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevSkillUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevSkillUserPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevSkillUser');
    }

    public function view(AuthUser $authUser, PrevSkillUser $prevSkillUser): bool
    {
        return $authUser->can('View:PrevSkillUser');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevSkillUser');
    }

    public function update(AuthUser $authUser, PrevSkillUser $prevSkillUser): bool
    {
        return $authUser->can('Update:PrevSkillUser');
    }

    public function delete(AuthUser $authUser, PrevSkillUser $prevSkillUser): bool
    {
        return $authUser->can('Delete:PrevSkillUser');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevSkillUser');
    }

    public function restore(AuthUser $authUser, PrevSkillUser $prevSkillUser): bool
    {
        return $authUser->can('Restore:PrevSkillUser');
    }

    public function forceDelete(AuthUser $authUser, PrevSkillUser $prevSkillUser): bool
    {
        return $authUser->can('ForceDelete:PrevSkillUser');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevSkillUser');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevSkillUser');
    }

    public function replicate(AuthUser $authUser, PrevSkillUser $prevSkillUser): bool
    {
        return $authUser->can('Replicate:PrevSkillUser');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevSkillUser');
    }

}