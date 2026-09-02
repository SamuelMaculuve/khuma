<?php

namespace App\Policies;

use App\Models\Clients;
use App\Models\User;

class ClientPolicy
{
    public function view(User $user, Clients $client): bool
    {
        return $this->belongsToCompany($user, $client);
    }

    public function update(User $user, Clients $client): bool
    {
        return $this->belongsToCompany($user, $client);
    }

    public function delete(User $user, Clients $client): bool
    {
        return $this->belongsToCompany($user, $client);
    }

    private function belongsToCompany(User $user, Clients $client): bool
    {
        return $user->company_id !== null
            && (int) $client->company_id === (int) $user->company_id;
    }
}
