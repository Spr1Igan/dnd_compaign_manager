<?php

namespace Tests\Unit;

use App\Models\Character;
use App\Models\Race;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacterArmorClassTest extends TestCase
{
    use RefreshDatabase;

    public function test_effective_armor_class_uses_dexterity_and_race_bonus_for_base_ac(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'login' => 'tester',
            'password' => 'password',
        ]);

        $race = Race::create([
            'name' => 'Эльф',
            'slug' => 'elf',
            'speed' => 30,
            'size' => 'Средний',
            'languages' => ['common', 'elvish'],
            'features' => [],
            'ability_bonuses' => ['dexterity' => 2],
        ]);

        $character = Character::create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'name' => 'Ловкий герой',
            'dexterity' => 14,
            'armor_class' => 10,
        ])->load('race');

        $this->assertSame(16, $character->totalAbilityScore('dexterity'));
        $this->assertSame(3, $character->dexterity_modifier);
        $this->assertSame(13, $character->baseArmorClass());
        $this->assertSame(13, $character->effectiveArmorClass());
    }

    public function test_effective_armor_class_keeps_manual_ac(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'login' => 'tester',
            'password' => 'password',
        ]);

        $character = Character::create([
            'user_id' => $user->id,
            'name' => 'Воин в доспехе',
            'dexterity' => 14,
            'armor_class' => 16,
        ]);

        $this->assertSame(12, $character->baseArmorClass());
        $this->assertFalse($character->usesBaseArmorClass());
        $this->assertSame(16, $character->effectiveArmorClass());
    }
}
