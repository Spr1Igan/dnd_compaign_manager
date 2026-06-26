<?php

namespace Tests\Unit;

use App\Models\Character;
use App\Models\Race;
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
}
