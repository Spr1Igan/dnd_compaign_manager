<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameMasterCharacterAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_master_can_see_other_players_characters(): void
    {
        $owner = User::create([
            'name' => 'Player One',
            'login' => 'player-one',
            'password' => 'password',
        ]);

        $gameMaster = User::create([
            'name' => 'Game Master',
            'login' => 'game-master',
            'password' => 'password',
            'role' => User::ROLE_GAME_MASTER,
        ]);

        $character = $this->characterFor($owner, ' чужой лист ');

        $this->actingAs($gameMaster)
            ->get(route('characters.index'))
            ->assertOk()
            ->assertSee($character->name)
            ->assertSee('Игрок: '.$owner->name);

        $this->actingAs($gameMaster)
            ->get(route('characters.show', $character))
            ->assertOk()
            ->assertSee($character->name)
            ->assertSee('Просмотр персонажа игрока '.$owner->name);
    }

    public function test_player_cannot_see_other_players_character(): void
    {
        $owner = User::create([
            'name' => 'Player One',
            'login' => 'player-one',
            'password' => 'password',
        ]);

        $otherPlayer = User::create([
            'name' => 'Player Two',
            'login' => 'player-two',
            'password' => 'password',
        ]);

        $character = $this->characterFor($owner, 'Hidden Hero');

        $this->actingAs($otherPlayer)
            ->get(route('characters.show', $character))
            ->assertForbidden();
    }

    public function test_game_master_cannot_manage_other_players_character(): void
    {
        $owner = User::create([
            'name' => 'Player One',
            'login' => 'player-one',
            'password' => 'password',
        ]);

        $gameMaster = User::create([
            'name' => 'Game Master',
            'login' => 'game-master',
            'password' => 'password',
            'role' => User::ROLE_GAME_MASTER,
        ]);

        $character = $this->characterFor($owner, 'Readonly Hero');

        $this->actingAs($gameMaster)
            ->get(route('characters.edit', $character))
            ->assertForbidden();

        $this->actingAs($gameMaster)
            ->patch(route('characters.vitals.update', $character), [
                'current_hp' => 1,
                'experience' => 999,
            ])
            ->assertForbidden();
    }

    private function characterFor(User $user, string $name): Character
    {
        return Character::create([
            'user_id' => $user->id,
            'name' => trim($name),
            'level' => 1,
            'experience' => 0,
            'strength' => 10,
            'dexterity' => 10,
            'constitution' => 10,
            'intelligence' => 10,
            'wisdom' => 10,
            'charisma' => 10,
            'max_hp' => 8,
            'current_hp' => 8,
            'armor_class' => 10,
            'speed' => 30,
            'skill_proficiencies' => [],
            'language_proficiencies' => [],
            'custom_armor_proficiencies' => [],
            'custom_weapon_proficiencies' => [],
            'custom_tool_proficiencies' => [],
            'equipment' => [],
            'features' => [],
        ]);
    }
}
