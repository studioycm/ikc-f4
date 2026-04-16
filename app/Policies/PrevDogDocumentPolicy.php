<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevDogDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevDogDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevDogDocument');
    }

    public function view(AuthUser $authUser, PrevDogDocument $prevDogDocument): bool
    {
        return $authUser->can('View:PrevDogDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevDogDocument');
    }

    public function update(AuthUser $authUser, PrevDogDocument $prevDogDocument): bool
    {
        return $authUser->can('Update:PrevDogDocument');
    }

    public function delete(AuthUser $authUser, PrevDogDocument $prevDogDocument): bool
    {
        return $authUser->can('Delete:PrevDogDocument');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevDogDocument');
    }

    public function restore(AuthUser $authUser, PrevDogDocument $prevDogDocument): bool
    {
        return $authUser->can('Restore:PrevDogDocument');
    }

    public function forceDelete(AuthUser $authUser, PrevDogDocument $prevDogDocument): bool
    {
        return $authUser->can('ForceDelete:PrevDogDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevDogDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevDogDocument');
    }

    public function replicate(AuthUser $authUser, PrevDogDocument $prevDogDocument): bool
    {
        return $authUser->can('Replicate:PrevDogDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevDogDocument');
    }

}