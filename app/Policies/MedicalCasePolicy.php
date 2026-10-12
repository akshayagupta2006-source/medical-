<?php

namespace App\Policies;

use App\Models\MedicalCase;
use App\Models\User;

class MedicalCasePolicy
{
    /**
     * Determine whether the user can view any cases.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isViewer();
    }

    /**
     * Determine whether the user can view the specific case.
     */
    public function view(User $user, MedicalCase $case): bool
    {
        return $user->isAdmin() || $user->isViewer();
    }

    /**
     * Determine whether the user can create cases.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the case.
     */
    public function update(User $user, MedicalCase $case): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the case.
     */
    public function delete(User $user, MedicalCase $case): bool
    {
        return $user->isAdmin();
    }
}
