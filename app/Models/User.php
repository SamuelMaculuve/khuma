<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'status','company_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => Role::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Companies::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Clients::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Messages::class, 'sender_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Notes::class);
    }

    public function leadHistories(): HasMany
    {
        return $this->hasMany(LeadHistories::class);
    }

    public function instance()
    {
        return $this->hasOne(Instance::class);
    }
    public function subscriptions()
    {
        return $this->hasOne(Subscription::class);
    }
}
