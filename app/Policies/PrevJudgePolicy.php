<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevJudge;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevJudgePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevJudge');
    }

    public function view(AuthUser $authUser, PrevJudge $prevJudge): bool
    {
        return $authUser->can('View:PrevJudge');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevJudge');
    }

    public function update(AuthUser $authUser, PrevJudge $prevJudge): bool
    {
        return $authUser->can('Update:PrevJudge');
    }

    public function delete(AuthUser $authUser, PrevJudge $prevJudge): bool
    {
        return $authUser->can('Delete:PrevJudge');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevJudge');
    }

    public function restore(AuthUser $authUser, PrevJudge $prevJudge): bool
    {
        return $authUser->can('Restore:PrevJudge');
    }

    public function forceDelete(AuthUser $authUser, PrevJudge $prevJudge): bool
    {
        return $authUser->can('ForceDelete:PrevJudge');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevJudge');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevJudge');
    }

    public function replicate(AuthUser $authUser, PrevJudge $prevJudge): bool
    {
        return $authUser->can('Replicate:PrevJudge');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevJudge');
    }

}