<?php

namespace Database\Seeders;

use App\Models\Race;
use Illuminate\Database\Seeder;

class RaceSeeder extends Seeder
{
    public function run(): void
    {
        $races = [
            [
                'name' => 'Человек',
                'slug' => 'human',
                'description' => 'Люди универсальны, разнообразны и быстро приспосабливаются к любым землям.',
                'speed' => 30,
                'size' => 'Средний',
                'languages' => ['common'],
                'features' => [],
                'ability_bonuses' => [
                    'strength' => 1,
                    'dexterity' => 1,
                    'constitution' => 1,
                    'intelligence' => 1,
                    'wisdom' => 1,
                    'charisma' => 1,
                ],
            ],
            [
                'name' => 'Эльф',
                'slug' => 'elf',
                'description' => 'Долгоживущий народ, связанный с магией, природой и древними традициями.',
                'speed' => 30,
                'size' => 'Средний',
                'languages' => ['common', 'elvish'],
                'features' => ['darkvision', 'keen-senses', 'fey-ancestry', 'trance'],
                'ability_bonuses' => ['dexterity' => 2],
            ],
            [
                'name' => 'Дварф',
                'slug' => 'dwarf',
                'description' => 'Крепкий народ мастеров, известный стойкостью, честью и ремеслом.',
                'speed' => 25,
                'size' => 'Средний',
                'languages' => ['common', 'dwarvish'],
                'features' => ['darkvision', 'dwarven-resilience', 'stonecunning'],
                'ability_bonuses' => ['constitution' => 2],
            ],
            [
                'name' => 'Полурослик',
                'slug' => 'halfling',
                'description' => 'Небольшой, ловкий и удивительно удачливый народ.',
                'speed' => 25,
                'size' => 'Маленький',
                'languages' => ['common', 'halfling'],
                'features' => ['lucky', 'brave', 'halfling-nimbleness'],
                'ability_bonuses' => ['dexterity' => 2],
            ],
            [
                'name' => 'Драконорождённый',
                'slug' => 'dragonborn',
                'description' => 'Гордый народ, несущий наследие драконов.',
                'speed' => 30,
                'size' => 'Средний',
                'languages' => ['common', 'draconic'],
                'features' => ['draconic-ancestry', 'breath-weapon', 'damage-resistance'],
                'ability_bonuses' => ['strength' => 2, 'charisma' => 1],
            ],
            [
                'name' => 'Гном',
                'slug' => 'gnome',
                'description' => 'Маленький народ с живым умом, любопытством и склонностью к магии.',
                'speed' => 25,
                'size' => 'Маленький',
                'languages' => ['common', 'gnomish'],
                'features' => ['darkvision', 'gnome-cunning'],
                'ability_bonuses' => ['intelligence' => 2],
            ],
            [
                'name' => 'Полуэльф',
                'slug' => 'half-elf',
                'description' => 'Соединяет черты людей и эльфов.',
                'speed' => 30,
                'size' => 'Средний',
                'languages' => ['common', 'elvish'],
                'features' => ['darkvision', 'fey-ancestry', 'skill-versatility'],
                'ability_bonuses' => ['charisma' => 2],
            ],
            [
                'name' => 'Полуорк',
                'slug' => 'half-orc',
                'description' => 'Сильный, выносливый и часто суровый герой.',
                'speed' => 30,
                'size' => 'Средний',
                'languages' => ['common', 'orc'],
                'features' => ['darkvision', 'menacing', 'relentless-endurance', 'savage-attacks'],
                'ability_bonuses' => ['strength' => 2, 'constitution' => 1],
            ],
            [
                'name' => 'Тифлинг',
                'slug' => 'tiefling',
                'description' => 'Несёт наследие нижних планов и врождённую магию.',
                'speed' => 30,
                'size' => 'Средний',
                'languages' => ['common', 'infernal'],
                'features' => ['darkvision', 'hellish-resistance', 'infernal-legacy'],
                'ability_bonuses' => ['intelligence' => 1, 'charisma' => 2],
            ],
        ];

        foreach ($races as $race) {
            Race::updateOrCreate(['slug' => $race['slug']], $race);
        }
    }
}
