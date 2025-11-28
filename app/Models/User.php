<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'qualification',
        'joining_date',
        'salary',
        'department',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth'     => 'date',
            'joining_date'      => 'date',
            'salary'            => 'decimal:2',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    // Helper: Get role name easily
    public function getRoleNameAttribute()
    {
        return $this->roles->pluck('name')->first() ?? 'No Role';
    }

    // Relation to Doctor extra info (if doctor)
  
}