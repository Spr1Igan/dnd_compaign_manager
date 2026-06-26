<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterClass extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'hit_die',
        'saving_throws',
        'armor_proficiencies',
        'weapon_proficiencies',
        'tool_proficiencies',
        'skill_choices',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'saving_throws' => 'array',
            'armor_proficiencies' => 'array',
            'weapon_proficiencies' => 'array',
            'tool_proficiencies' => 'array',
            'skill_choices' => 'array',
            'features' => 'array',
        ];
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'class_id');
    }
}