<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\PlanPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $plans = [
            'ubuntu' => [
                'name' => 'Ubuntu',
                'description' => 'Plano básico para começar com CRM, WhatsApp e relatórios.',
                'amount' => 1500,
                'features' => [
                    'crm' => 1,
                    'whatsapp' => 1,
                    'reports' => 1,
                    'settings' => 1,
                    'members' => 2,
                    'chatbot_lines' => 500,
                    'whatsapp_instances' => 1,
                    'email_campaigns' => 0,
                    'team_management' => 0,
                    'call_logs' => 0,
                    'product_sales' => 0,
                    'bulk_messages' => 0,
                ],
            ],
            'baoba' => [
                'name' => 'Baobá',
                'description' => 'Plano intermédio para equipas em crescimento.',
                'amount' => 8000,
                'features' => [
                    'crm' => 1,
                    'whatsapp' => 1,
                    'email_campaigns' => 1,
                    'reports' => 1,
                    'settings' => 1,
                    'team_management' => 1,
                    'call_logs' => 1,
                    'members' => 5,
                    'chatbot_lines' => 1500,
                    'whatsapp_instances' => 2,
                    'product_sales' => 1,
                    'bulk_messages' => 1,
                ],
            ],
            'leao' => [
                'name' => 'Leão',
                'description' => 'Plano premium para operações estabelecidas.',
                'amount' => 25000,
                'features' => [
                    'crm' => 1,
                    'whatsapp' => 1,
                    'email_campaigns' => 1,
                    'reports' => 1,
                    'settings' => 1,
                    'team_management' => 1,
                    'call_logs' => 1,
                    'members' => 'unlimited',
                    'chatbot_lines' => 'unlimited',
                    'whatsapp_instances' => 'unlimited',
                    'product_sales' => 1,
                    'bulk_messages' => 1,
                    'whatsapp_templates' => 1,
                    'priority_support' => 1,
                ],
            ],
        ];

        foreach ($plans as $code => $data) {
            $plan = Plan::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]
            );

            PlanPrice::where('plan_id', $plan->id)->update(['is_active' => false]);

            PlanPrice::updateOrCreate(
                [
                    'plan_id' => $plan->id,
                    'amount' => $data['amount'],
                    'currency' => 'MZN',
                ],
                [
                    'is_active' => true,
                    'starts_at' => now(),
                    'ends_at' => null,
                ]
            );

            foreach ($data['features'] as $key => $value) {
                PlanFeature::updateOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'feature_key' => $key,
                    ],
                    ['feature_value' => $value]
                );
            }
        }
    }
}
