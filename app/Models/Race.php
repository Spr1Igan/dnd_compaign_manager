<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'speed',
        'size',
        'languages',
        'features',
        'ability_bonuses',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'features' => 'array',
            'ability_bonuses' => 'array',
        ];
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }
}