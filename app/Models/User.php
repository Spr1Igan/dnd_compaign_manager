<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    public const ROLE_PLAYER = 'player';
    public const ROLE_GAME_MASTER = 'game_master';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'login',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function isGameMaster(): bool
    {
        return $this->role === self::ROLE_GAME_MASTER;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
