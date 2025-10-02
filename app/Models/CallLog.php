<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'number',
        'type',
        'started_at',
        'duration_seconds',
        'raw',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'raw' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
