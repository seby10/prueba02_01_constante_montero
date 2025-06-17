<?php

namespace App\Data\MySQL\Models;

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
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded ?: ['USER_ROLE'];
        }
        return is_array($value) ? $value : ['USER_ROLE'];
    }

    public function setRolesAttribute($value): void
    {
        $this->attributes['roles'] = is_array($value) ? json_encode($value) : $value;
    }
}