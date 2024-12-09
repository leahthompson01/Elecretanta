<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hobby extends Model
{
    /** @use HasFactory<\Database\Factories\HobbyFactory> */
    use HasFactory;
    protected $fillable = ['name'];
    protected $keyType = 'string';
    protected $primaryKey = 'name';
    public $incrementing = false;
    public $timestamps = false;
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    protected function casts(): array 
    {
        return [
            'name' => 'string'
        ];
    }
}
