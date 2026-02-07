<?php

namespace Database\Seeders;

use App\Models\Plan;
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
            'ubuntu' => ['Ubuntu', 'Plano básico'],
            'baoba'  => ['Baobá', 'Plano intermédio'],
            'leao'   => ['Leão', 'Plano premium'],
        ];

        foreach ($plans as $code => [$name, $desc]) {
            $plan = Plan::create([
                'code' => $code,
                'name' => $name,
                'description' => $desc,
            ]);

            PlanPrice::create([
                'plan_id' => $plan->id,
                'amount' => match ($code) {
                    'ubuntu' => 1500,
                    'baoba' => 8000,
                    'leao' => 25000,
                },
                'is_active' => true,
            ]);

            if ($plan->code === 'ubuntu') {
                $plan->features()->createMany([
                    ['feature_key' => 'members', 'feature_value' => 2],
                    ['feature_key' => 'whatsapp_instances', 'feature_value' => 1],
                ]);
            }
            if ($plan->code === 'baoba') {
                $plan->features()->createMany([
                    ['feature_key' => 'members', 'feature_value' => 5],
                    ['feature_key' => 'chatbot_lines', 'feature_value' => 500],
                ]);
            }

            if ($plan->code === 'leao') {
                $plan->features()->createMany([
                    ['feature_key' => 'members', 'feature_value' => 'unlimited'],
                    ['feature_key' => 'chatbot_lines', 'feature_value' => 'unlimited'],
                ]);
            }
        }
    }
}
