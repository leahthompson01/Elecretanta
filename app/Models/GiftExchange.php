<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftExchange extends Model
{
    protected $fillable = [
        'gifts',
        'user_id'
    ];

}