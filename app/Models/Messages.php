<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Messages extends Model
{
    protected $guarded = [];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Leads::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function messages()
    {
        return $this->hasMany(Messages::class, 'sender_id');
    }
    public function instance()
    {
        return $this->belongsTo(Instance::class, 'sender_id');
    }
}
