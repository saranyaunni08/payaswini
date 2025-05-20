<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'role_id',
        'username',
        'password',
        'email',
        'full_name',
        'gender',
        'is_active',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}