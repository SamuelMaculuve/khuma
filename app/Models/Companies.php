<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Companies extends Model
{

//    protected $fillable = ['name','tax_number','address','phone','email'];
    protected $guarded = [];
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
