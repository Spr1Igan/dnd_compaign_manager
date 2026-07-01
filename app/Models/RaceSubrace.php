<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaceSubrace extends Model
{
    protected $fillable = [
        'race_id',
        'name',
        'slug',
        'description',
        'speed',
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

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'subrace_id');
    }
}
