<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevDogImport;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevDogImportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevDogImport');
    }

    public function view(AuthUser $authUser, PrevDogImport $prevDogImport): bool
    {
        return $authUser->can('View:PrevDogImport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevDogImport');
    }

    public function update(AuthUser $authUser, PrevDogImport $prevDogImport): bool
    {
        return $authUser->can('Update:PrevDogImport');
    }

    public function delete(AuthUser $authUser, PrevDogImport $prevDogImport): bool
    {
        return $authUser->can('Delete:PrevDogImport');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevDogImport');
    }

    public function restore(AuthUser $authUser, PrevDogImport $prevDogImport): bool
    {
        return $authUser->can('Restore:PrevDogImport');
    }

    public function forceDelete(AuthUser $authUser, PrevDogImport $prevDogImport): bool
    {
        return $authUser->can('ForceDelete:PrevDogImport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevDogImport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevDogImport');
    }

    public function replicate(AuthUser $authUser, PrevDogImport $prevDogImport): bool
    {
        return $authUser->can('Replicate:PrevDogImport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevDogImport');
    }

}