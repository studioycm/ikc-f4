<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BreedingInquiry;
use Illuminate\Auth\Access\HandlesAuthorization;

class BreedingInquiryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BreedingInquiry');
    }

    public function view(AuthUser $authUser, BreedingInquiry $breedingInquiry): bool
    {
        return $authUser->can('View:BreedingInquiry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BreedingInquiry');
    }

    public function update(AuthUser $authUser, BreedingInquiry $breedingInquiry): bool
    {
        return $authUser->can('Update:BreedingInquiry');
    }

    public function delete(AuthUser $authUser, BreedingInquiry $breedingInquiry): bool
    {
        return $authUser->can('Delete:BreedingInquiry');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BreedingInquiry');
    }

    public function restore(AuthUser $authUser, BreedingInquiry $breedingInquiry): bool
    {
        return $authUser->can('Restore:BreedingInquiry');
    }

    public function forceDelete(AuthUser $authUser, BreedingInquiry $breedingInquiry): bool
    {
        return $authUser->can('ForceDelete:BreedingInquiry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BreedingInquiry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BreedingInquiry');
    }

    public function replicate(AuthUser $authUser, BreedingInquiry $breedingInquiry): bool
    {
        return $authUser->can('Replicate:BreedingInquiry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BreedingInquiry');
    }

}