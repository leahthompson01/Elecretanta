<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeGroup extends Model
{
    protected $table = 'exchange_groups';
    protected $fillable = ["name", "budget", 'exchangeDate'];

    public $incrementing = true;

    public $timestamps = true;

    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    protected $casts = [
        'budget'=> 'float',
        'exchangeDate' => 'datetime',
        'name'=> 'string'
    ];
}
