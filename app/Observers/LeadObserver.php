<?php
// LeadObserver.php
namespace App\Observers;


use App\Models\LeadHistories;
use App\Models\Leads;

class LeadObserver
{
    public function updated(Leads $lead)
    {
        $changes = $lead->getChanges();
        $original = $lead->getOriginal();

        if (isset($changes['status'])) {
            LeadHistories::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'action' => 'status_changed',
                'old_data' => ['status' => $original['status']],
                'new_data' => ['status' => $changes['status']],
                'description' => "Status changed from {$original['status']} to {$changes['status']}",
            ]);
        }
    }

    public function created(Leads $lead)
    {
        LeadHistories::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'action' => 'lead_created',
            'description' => 'Lead was created',
        ]);
    }
}
