<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailCampaign extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'filters'      => 'array',
        'images'       => 'array',
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public const REPLY_RECORD_ONLY = 'record_only';
    public const REPLY_CREATE_IF_NONE = 'create_lead_if_none';
    public const REPLY_ALWAYS_CREATE = 'always_create_lead';

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Companies::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function replyTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'reply_team_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmailCampaignLog::class);
    }
}
