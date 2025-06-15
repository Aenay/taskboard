<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function updateStatus(User $user, Project $project): bool
    {
        return $user->hasRole('admin') || $user->id === $project->manager_id;
    }
}
