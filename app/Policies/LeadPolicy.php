<?php

namespace App\Policies;

use App\Models\Leads;
use App\Models\User;

class LeadPolicy
{
    public function view(User $user, Leads $lead): bool
    {
        return $this->belongsToCompany($user, $lead);
    }

    public function update(User $user, Leads $lead): bool
    {
        return $this->belongsToCompany($user, $lead);
    }

    public function delete(User $user, Leads $lead): bool
    {
        return $this->belongsToCompany($user, $lead);
    }

    private function belongsToCompany(User $user, Leads $lead): bool
    {
        return $user->company_id !== null
            && (int) $lead->company_id === (int) $user->company_id;
    }
}
