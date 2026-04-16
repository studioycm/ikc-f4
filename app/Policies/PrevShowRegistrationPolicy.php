<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrevShowRegistration;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowRegistrationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PrevShowRegistration');
    }

    public function view(AuthUser $authUser, PrevShowRegistration $prevShowRegistration): bool
    {
        return $authUser->can('View:PrevShowRegistration');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrevShowRegistration');
    }

    public function update(AuthUser $authUser, PrevShowRegistration $prevShowRegistration): bool
    {
        return $authUser->can('Update:PrevShowRegistration');
    }

    public function delete(AuthUser $authUser, PrevShowRegistration $prevShowRegistration): bool
    {
        return $authUser->can('Delete:PrevShowRegistration');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PrevShowRegistration');
    }

    public function restore(AuthUser $authUser, PrevShowRegistration $prevShowRegistration): bool
    {
        return $authUser->can('Restore:PrevShowRegistration');
    }

    public function forceDelete(AuthUser $authUser, PrevShowRegistration $prevShowRegistration): bool
    {
        return $authUser->can('ForceDelete:PrevShowRegistration');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrevShowRegistration');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrevShowRegistration');
    }

    public function replicate(AuthUser $authUser, PrevShowRegistration $prevShowRegistration): bool
    {
        return $authUser->can('Replicate:PrevShowRegistration');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrevShowRegistration');
    }

}