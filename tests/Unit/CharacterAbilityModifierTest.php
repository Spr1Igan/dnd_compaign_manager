<?php

namespace Tests\Unit;

use App\Models\Character;
use App\Models\Race;
use App\Models\RaceSubrace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacterAbilityModifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_ability_score_is_capped_at_thirty_before_modifier_is_calculated(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'login' => 'ability-tester',
            'password' => 'password',
        ]);

        $race = Race::create([
            'name' => 'Гном',
            'slug' => 'gnome',
            'speed' => 25,
            'size' => 'Маленький',
            'languages' => ['common', 'gnomish'],
            'features' => [],
            'ability_bonuses' => ['intelligence' => 2],
        ]);

        $character = Character::create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'name' => 'Мудрый максимум',
            'intelligence' => 30,
        ])->load('race');

        $this->assertSame(30, $character->totalAbilityScore('intelligence'));
        $this->assertSame(10, $character->intelligence_modifier);
    }

    public function test_total_ability_score_uses_race_and_subrace_bonuses(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'login' => 'subrace-tester',
            'password' => 'password',
        ]);

        $race = Race::create([
            'name' => 'Elf',
            'slug' => 'elf',
            'speed' => 30,
            'size' => 'Medium',
            'languages' => ['common', 'elvish'],
            'features' => [],
            'ability_bonuses' => ['dexterity' => 2],
        ]);

        $subrace = RaceSubrace::create([
            'race_id' => $race->id,
            'name' => 'Wood Elf',
            'slug' => 'wood-elf',
            'speed' => 35,
            'languages' => [],
            'features' => [],
            'ability_bonuses' => ['wisdom' => 1],
        ]);

        $character = Character::create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'subrace_id' => $subrace->id,
            'name' => 'Subrace Hero',
            'dexterity' => 14,
            'wisdom' => 13,
        ])->load(['race', 'subrace']);

        $this->assertSame(16, $character->totalAbilityScore('dexterity'));
        $this->assertSame(14, $character->totalAbilityScore('wisdom'));
    }
}
