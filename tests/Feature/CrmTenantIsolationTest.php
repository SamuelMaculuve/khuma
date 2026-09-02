<?php

namespace Tests\Feature;

use App\Livewire\KanbanBoard;
use App\Models\Clients;
use App\Models\Companies;
use App\Models\Leads;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CrmTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function tenantUser(string $suffix, Companies $company): User
    {
        return User::create([
            'name' => "User {$suffix}",
            'email' => "user-{$suffix}@example.test",
            'password' => 'password',
            'status' => 'active',
            'company_id' => $company->id,
        ]);
    }

    private function company(string $name): Companies
    {
        return Companies::create(['name' => $name]);
    }

    private function client(Companies $company, string $name): Clients
    {
        return Clients::create([
            'company_id' => $company->id,
            'name' => $name,
            'email' => strtolower(str_replace(' ', '-', $name)) . '@example.test',
        ]);
    }

    private function lead(Companies $company, Clients $client, ?Team $team = null, string $title = 'Lead'): Leads
    {
        return Leads::create([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'team_id' => $team?->id,
            'reference' => 'REF-' . uniqid(),
            'title' => $title,
            'status' => 'new',
        ]);
    }

    public function test_kanban_lists_only_leads_of_the_authenticated_company(): void
    {
        $companyA = $this->company('Company A');
        $companyB = $this->company('Company B');
        $userA = $this->tenantUser('a', $companyA);

        $clientA = $this->client($companyA, 'Client A');
        $clientB = $this->client($companyB, 'Client B');
        $leadA = $this->lead($companyA, $clientA, null, 'Lead A');
        $leadB = $this->lead($companyB, $clientB, null, 'Lead B');

        Livewire::actingAs($userA)
            ->test(KanbanBoard::class)
            ->assertSee('Lead A')
            ->assertDontSee('Lead B')
            ->assertSet('states.new.0.id', $leadA->id);

        $this->assertDatabaseHas('leads', ['id' => $leadB->id, 'company_id' => $companyB->id]);
    }

    public function test_direct_lead_url_cannot_open_a_lead_from_another_company(): void
    {
        $companyA = $this->company('Company A');
        $companyB = $this->company('Company B');
        $userA = $this->tenantUser('a-url', $companyA);

        $clientB = $this->client($companyB, 'Client B');
        $leadB = $this->lead($companyB, $clientB, null, 'Private Lead B');

        $response = $this->actingAs($userA)
        ->get(route('lead.show', $leadB));

        dump($response->headers->get('Location'));

        $response->assertStatus(302);
    }

    public function test_direct_update_route_cannot_update_another_company_lead(): void
    {
        $companyA = $this->company('Company A');
        $companyB = $this->company('Company B');
        $userA = $this->tenantUser('a-update', $companyA);

        $clientB = $this->client($companyB, 'Client B');
        $leadB = $this->lead($companyB, $clientB, null, 'Private Lead B');

        $response = $this->actingAs($userA)
        ->put(route('lead.update', $leadB), [
        'title' => 'Hacked',
             ]);

        dump($response->headers->get('Location'));

        $response->assertStatus(302);

        $this->assertDatabaseHas('leads', [
            'id' => $leadB->id,
            'company_id' => $companyB->id,
            'title' => 'Private Lead B',
        ]);
    }

    public function test_kanban_rejects_cross_company_client_and_team_associations(): void
    {
        $companyA = $this->company('Company A');
        $companyB = $this->company('Company B');
        $userA = $this->tenantUser('a-association', $companyA);

        $clientA = $this->client($companyA, 'Client A');
        $clientB = $this->client($companyB, 'Client B');
        $teamB = Team::create(['company_id' => $companyB->id, 'name' => 'Team B']);

        Livewire::actingAs($userA)
            ->test(KanbanBoard::class)
            ->set('lead_client_id', $clientB->id)
            ->set('lead_team_id', $teamB->id)
            ->set('lead_title', 'Cross tenant')
            ->set('leadStatus', 'new')
            ->call('saveLead')
            ->assertHasErrors(['lead_client_id', 'lead_team_id']);

        $this->assertDatabaseMissing('leads', ['title' => 'Cross tenant']);

        // A valid association within the current company still works.
        $teamA = Team::create(['company_id' => $companyA->id, 'name' => 'Team A']);

        Livewire::actingAs($userA)
            ->test(KanbanBoard::class)
            ->set('lead_client_id', $clientA->id)
            ->set('lead_team_id', $teamA->id)
            ->set('lead_title', 'Valid tenant lead')
            ->set('leadStatus', 'new')
            ->call('saveLead')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leads', [
            'title' => 'Valid tenant lead',
            'company_id' => $companyA->id,
            'client_id' => $clientA->id,
            'team_id' => $teamA->id,
        ]);
    }

    public function test_kanban_cannot_move_a_lead_from_another_company(): void
    {
        $companyA = $this->company('Company A');
        $companyB = $this->company('Company B');
        $userA = $this->tenantUser('a-move', $companyA);

        $clientB = $this->client($companyB, 'Client B');
        $leadB = $this->lead($companyB, $clientB, null, 'Private Lead B');

        Livewire::actingAs($userA)
            ->test(KanbanBoard::class)
            ->call('moveItem', $leadB->id, 'new', 'won');

        $this->assertDatabaseHas('leads', [
            'id' => $leadB->id,
            'company_id' => $companyB->id,
            'status' => 'new',
        ]);
    }
}
