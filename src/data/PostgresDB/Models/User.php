<?php

namespace App\Data\PostgresDB\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email', 
        'password',
        'img',
        'roles'
    ];

    protected $casts = [
        'roles' => 'array'
    ];

    // protected $hidden = [
    //     'password'
    // ];

    public function getRolesAttribute($value): array
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public function setRolesAttribute($value): void
    {
        $this->attributes['roles'] = is_array($value) ? json_encode($value) : $value;
    }
}
