<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\CharacterClass;
use App\Models\CharacterSubclass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacterSubclassFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_character_store_adds_subclass_features_available_for_level(): void
    {
        $user = User::create([
            'name' => 'Tester',
            'login' => 'subclass-tester',
            'password' => 'password',
        ]);

        $class = CharacterClass::create([
            'name' => 'Fighter',
            'slug' => 'fighter',
            'hit_die' => 10,
            'saving_throws' => ['strength', 'constitution'],
            'armor_proficiencies' => [],
            'weapon_proficiencies' => [],
            'tool_proficiencies' => [],
            'skill_choices' => ['choose' => 2, 'from' => ['athletics']],
            'features' => ['second-wind'],
        ]);

        $subclass = CharacterSubclass::create([
            'class_id' => $class->id,
            'name' => 'Champion',
            'slug' => 'champion',
            'features_by_level' => [
                3 => ['improved-critical'],
                7 => ['remarkable-athlete'],
            ],
        ]);

        $this->actingAs($user)
            ->post(route('characters.store'), [
                'name' => 'Champion Hero',
                'class_id' => $class->id,
                'subclass_id' => $subclass->id,
                'level' => 3,
                'strength' => 10,
                'dexterity' => 10,
                'constitution' => 10,
                'intelligence' => 10,
                'wisdom' => 10,
                'charisma' => 10,
            ])
            ->assertRedirect();

        $character = Character::query()->where('name', 'Champion Hero')->firstOrFail();

        $this->assertSame($subclass->id, $character->subclass_id);
        $this->assertContains('second-wind', $character->features);
        $this->assertContains('improved-critical', $character->features);
        $this->assertNotContains('remarkable-athlete', $character->features);
    }
}
