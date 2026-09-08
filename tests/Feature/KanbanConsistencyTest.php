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

class KanbanConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private function company(string $name): Companies
    {
        return Companies::create(['name' => $name]);
    }

    private function user(Companies $company): User
    {
        return User::create([
            'name' => 'Kanban User',
            'email' => uniqid('kanban-', true) . '@example.test',
            'password' => 'password',
            'status' => 'active',
            'company_id' => $company->id,
        ]);
    }

    private function client(Companies $company, string $name = 'Client'): Clients
    {
        return Clients::create([
            'company_id' => $company->id,
            'name' => $name,
            'email' => uniqid('client-', true) . '@example.test',
        ]);
    }

    private function lead(Companies $company, Clients $client, array $extra = []): Leads
    {
        return Leads::create(array_merge([
            'client_id' => $client->id,
            'company_id' => $company->id,
            'reference' => 'REF-' . uniqid(),
            'title' => 'Lead',
            'status' => 'new',
        ], $extra));
    }

    public function test_supported_statuses_are_the_single_kanban_source_of_truth(): void
    {
        $this->assertSame(
            ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'],
            Leads::SUPPORTED_STATUSES
        );

        $company = $this->company('Company');
        $user = $this->user($company);

        Livewire::actingAs($user)
            ->test(KanbanBoard::class)
            ->assertSet('states', array_fill_keys(Leads::SUPPORTED_STATUSES, []));
    }

    public function test_invalid_status_cannot_be_created(): void
    {
        $company = $this->company('Company');
        $user = $this->user($company);
        $client = $this->client($company);

        Livewire::actingAs($user)
            ->test(KanbanBoard::class)
            ->set('lead_client_id', $client->id)
            ->set('lead_title', 'Invalid status lead')
            ->set('leadStatus', 'custom-status')
            ->call('saveLead')
            ->assertHasErrors(['leadStatus']);

        $this->assertDatabaseMissing('leads', ['title' => 'Invalid status lead']);
    }

    public function test_move_rejects_unsupported_destination_without_changing_persisted_state(): void
    {
        $company = $this->company('Company');
        $user = $this->user($company);
        $client = $this->client($company);
        $lead = $this->lead($company, $client);

        Livewire::actingAs($user)
            ->test(KanbanBoard::class)
            ->call('moveItem', $lead->id, 'new', 'custom-status')
            ->assertHasErrors(['moveItem']);

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'company_id' => $company->id,
            'status' => 'new',
        ]);
    }

    public function test_valid_move_updates_database_and_rebuilds_board_from_persisted_state(): void
    {
        $company = $this->company('Company');
        $user = $this->user($company);
        $client = $this->client($company);
        $lead = $this->lead($company, $client);

        Livewire::actingAs($user)
            ->test(KanbanBoard::class)
            ->call('moveItem', $lead->id, 'new', 'qualified')
            ->assertHasNoErrors()
            ->assertSet('states.qualified.0.id', $lead->id)
            ->assertSet('states.new', []);

        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'qualified']);
    }

    public function test_failed_persistence_does_not_move_card_in_component_state(): void
    {
        $company = $this->company('Company');
        $user = $this->user($company);
        $client = $this->client($company);
        $lead = $this->lead($company, $client);

        Leads::updating(function (Leads $model) use ($lead) {
            if ($model->is($lead)) {
                return false;
            }
        });

        Livewire::actingAs($user)
            ->test(KanbanBoard::class)
            ->call('moveItem', $lead->id, 'new', 'won')
            ->assertHasErrors(['moveItem'])
            ->assertSet('states.new.0.id', $lead->id)
            ->assertSet('states.won', []);

        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'new']);
    }

    public function test_search_and_status_filter_use_real_lead_attributes(): void
    {
        $company = $this->company('Company');
        $user = $this->user($company);
        $clientA = $this->client($company, 'Acme Client');
        $clientB = $this->client($company, 'Other Client');
        $team = Team::create(['company_id' => $company->id, 'name' => 'Sales Team']);

        $sourceLead = $this->lead($company, $clientA, [
            'title' => 'CRM Opportunity',
            'source' => 'Website',
            'team_id' => $team->id,
        ]);
        $otherLead = $this->lead($company, $clientB, [
            'title' => 'Unrelated Opportunity',
            'status' => 'won',
        ]);

        Livewire::actingAs($user)
            ->test(KanbanBoard::class)
            ->set('search', 'Website')
            ->assertSee('CRM Opportunity')
            ->assertDontSee('Unrelated Opportunity')
            ->set('search', '')
            ->set('filterStatus', 'won')
            ->assertSee('Unrelated Opportunity')
            ->assertDontSee('CRM Opportunity');

        $this->assertDatabaseHas('leads', ['id' => $sourceLead->id, 'source' => 'Website']);
        $this->assertDatabaseHas('leads', ['id' => $otherLead->id, 'status' => 'won']);
    }
}
