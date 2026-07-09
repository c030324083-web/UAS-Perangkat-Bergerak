<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens; // 1. Tambahkan import Sanctum ini

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token', 'roles'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens; // 2. Tambahkan HasApiTokens di sini

    protected $appends = ['role']; 

    /**
     * Get the user's role name.
     */
    protected function role(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getRoleNames()->first() ?? 'Anggota',
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}