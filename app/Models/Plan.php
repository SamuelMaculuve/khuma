<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function feature($key)
    {
        return $this->features()
            ->where('feature_key', $key)
            ->first()?->feature_value;
    }

    public function featureLabel(string $key): ?string
    {
        return [
            'crm' => 'CRM e pipeline de leads',
            'email_campaigns' => 'Email marketing',
            'whatsapp' => 'WhatsApp',
            'reports' => 'Relatórios',
            'team_management' => 'Gestão de equipa',
            'settings' => 'Configurações',
            'call_logs' => 'Registos de chamadas',
            'members' => 'Membros da equipa',
            'chatbot_lines' => 'Linhas no chatbot',
            'whatsapp_instances' => 'Instâncias WhatsApp',
            'product_sales' => 'Venda de produtos/serviços',
            'bulk_messages' => 'Mensagens em massa',
            'whatsapp_templates' => 'WhatsApp Templates API',
            'priority_support' => 'Suporte prioritário',
        ][$key] ?? null;
    }

    public function includedFeatures()
    {
        return $this->features
            ->filter(fn (PlanFeature $feature) => $this->hasFeature($feature->feature_key));
    }

    public function hasFeature(string $key): bool
    {
        $value = $this->feature($key);

        return !blank($value) && !in_array($value, ['0', 'false', 'no'], true);
    }

    public function featureLimit(string $key, $default = null)
    {
        return $this->feature($key) ?? $default;
    }


    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function currentPrice()
    {
        return $this->prices()
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
