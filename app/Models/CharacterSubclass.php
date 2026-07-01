<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterSubclass extends Model
{
    protected $fillable = [
        'class_id',
        'name',
        'slug',
        'description',
        'features_by_level',
    ];

    protected function casts(): array
    {
        return [
            'features_by_level' => 'array',
        ];
    }

    public function characterClass(): BelongsTo
    {
        return $this->belongsTo(CharacterClass::class, 'class_id');
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'subclass_id');
    }
}
