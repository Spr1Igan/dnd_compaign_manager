<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\RaceSubrace;
use Illuminate\Database\Seeder;

class RaceSubraceSeeder extends Seeder
{
    public function run(): void
    {
        $subraces = [
            'dwarf' => [
                ['name' => 'Hill Dwarf', 'slug' => 'hill-dwarf', 'ability_bonuses' => ['wisdom' => 1], 'features' => ['dwarven-toughness']],
                ['name' => 'Mountain Dwarf', 'slug' => 'mountain-dwarf', 'ability_bonuses' => ['strength' => 2], 'features' => ['dwarven-armor-training']],
            ],
            'elf' => [
                ['name' => 'High Elf', 'slug' => 'high-elf', 'ability_bonuses' => ['intelligence' => 1], 'languages' => ['choose:1'], 'features' => ['elf-weapon-training', 'cantrip']],
                ['name' => 'Wood Elf', 'slug' => 'wood-elf', 'speed' => 35, 'ability_bonuses' => ['wisdom' => 1], 'features' => ['elf-weapon-training', 'fleet-of-foot', 'mask-of-the-wild']],
                ['name' => 'Dark Elf', 'slug' => 'dark-elf-drow', 'ability_bonuses' => ['charisma' => 1], 'features' => ['superior-darkvision', 'sunlight-sensitivity', 'drow-magic', 'drow-weapon-training']],
            ],
            'halfling' => [
                ['name' => 'Lightfoot Halfling', 'slug' => 'lightfoot-halfling', 'ability_bonuses' => ['charisma' => 1], 'features' => ['naturally-stealthy']],
                ['name' => 'Stout Halfling', 'slug' => 'stout-halfling', 'ability_bonuses' => ['constitution' => 1], 'features' => ['stout-resilience']],
            ],
            'gnome' => [
                ['name' => 'Forest Gnome', 'slug' => 'forest-gnome', 'ability_bonuses' => ['dexterity' => 1], 'features' => ['natural-illusionist', 'speak-with-small-beasts']],
                ['name' => 'Rock Gnome', 'slug' => 'rock-gnome', 'ability_bonuses' => ['constitution' => 1], 'features' => ['artificers-lore', 'tinker']],
            ],
        ];

        foreach ($subraces as $raceSlug => $items) {
            $race = Race::query()->where('slug', $raceSlug)->first();

            if (! $race) {
                continue;
            }

            foreach ($items as $subrace) {
                RaceSubrace::updateOrCreate(['slug' => $subrace['slug']], [
                    'race_id' => $race->id,
                    'name' => $subrace['name'],
                    'description' => null,
                    'speed' => $subrace['speed'] ?? null,
                    'languages' => $subrace['languages'] ?? [],
                    'features' => $subrace['features'] ?? [],
                    'ability_bonuses' => $subrace['ability_bonuses'] ?? [],
                ]);
            }
        }
    }
}
