<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // Modification of the project (Manager only)
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    // Modification of the "Done jobs" checkbox (Manager OR Team member)
    public function updateStatus(User $user, Project $project): bool
    {
        return $user->id === $project->user_id || $project->members->contains($user->id);
    }
}