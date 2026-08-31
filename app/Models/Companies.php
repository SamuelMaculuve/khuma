<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tax_number',
        'address',
        'email',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}