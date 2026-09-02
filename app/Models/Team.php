<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Team extends Model
{
    protected $guarded = [];

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where($this->getTable() . '.company_id', $companyId);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Companies::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    public function emailAddress(): string
    {
        $company = $this->company;
        if (! $company?->mail_subdomain || ! $this->email_alias) {
            return '';
        }
        $parent = config('services.mail_tenant.parent_domain');
        return "{$this->email_alias}@{$company->mail_subdomain}.{$parent}";
    }
}
