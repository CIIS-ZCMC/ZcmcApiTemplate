<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    protected $table = "user_details";

    public $fillable = [
        'user_details',
        'permissions',
        'token',
        'token_exp',
        'authorization_pin'
    ];

    public $timestamps = true;
    
    protected $casts = [
        'user_details' => 'array',
        'permissions' => 'array'
    ];
}
