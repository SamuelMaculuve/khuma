<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function view(User $user, Team $team): bool
    {
        return $this->belongsToCompany($user, $team);
    }

    public function update(User $user, Team $team): bool
    {
        return $this->belongsToCompany($user, $team);
    }

    public function delete(User $user, Team $team): bool
    {
        return $this->belongsToCompany($user, $team);
    }

    private function belongsToCompany(User $user, Team $team): bool
    {
        return $user->company_id !== null
            && (int) $team->company_id === (int) $user->company_id;
    }
}
