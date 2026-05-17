<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'full_name'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
