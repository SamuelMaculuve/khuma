<?php
namespace Database\Seeders;

use App\Models\Clients;
use App\Models\Companies;
use App\Models\Leads;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CompanyClientUserSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 2; $i++) {

            // =====================
            // COMPANY
            // =====================
            $company = Companies::create([
                'name' => "Company {$i}",
                'tax_number' => rand(100000000, 999999999),
                'address' => "Endereço da Company {$i}",
                'phone' => '25884' . rand(1000000, 9999999),
                'email' => "company{$i}@example.com",
            ]);

            // =====================
            // USER
            // =====================
            $subscriberRole = Role::firstOrCreate(['name' => 'subscriber']);

            $user = User::create([
                'name' => "User {$i}",
                'email' => "user{$i}@khuma.com",
                'password' => Hash::make('password'),
                'status' => 'active',
                'phone' => '25887' . rand(1000000, 9999999),
                'company_id' => $company->id,
            ]);

            $user->assignRole($subscriberRole);

            // =====================
            // CLIENTS (5 por company)
            // =====================
            for ($c = 1; $c <= 5; $c++) {

                $client = Clients::create([
                    'company_id' => $company->id,
                    'name' => "Client {$c} - Company {$i}",
                    'email' => "client{$c}_company{$i}@khuma.com",
                    'phone' => '25886' . rand(1000000, 9999999),
                    'address' => "Endereço do Client {$c}",
                ]);

                // =====================
                // LEADS (10 por client)
                // =====================
                for ($l = 1; $l <= 10; $l++) {
                    Leads::create([
                        'client_id' => $client->id,
                        'reference' => 'LEAD-' . strtoupper(uniqid()),
                        'title' => "Lead {$l} - Client {$c}",
                        'description' => "Descrição da lead {$l}",
                        'status' => collect(['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost'])->random(),
                        'value' => rand(5000, 100000),
                        'expected_close_date' => now()->addDays(rand(5, 90)),
                        'source' => collect(['website', 'whatsapp', 'email', 'referral'])->random(),
                        'company_id'=> $client->company_id
                    ]);
                }
            }
        }
    }
}


