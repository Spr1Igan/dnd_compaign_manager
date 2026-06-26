<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Background extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'skill_proficiencies',
        'tool_proficiencies',
        'languages',
        'equipment',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'skill_proficiencies' => 'array',
            'tool_proficiencies' => 'array',
            'languages' => 'array',
            'equipment' => 'array',
            'features' => 'array',
        ];
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }
}