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
            'dragonborn' => [
                ['name' => 'Black Dragon Ancestry', 'slug' => 'black-dragonborn', 'features' => ['black-dragon-ancestry', 'breath-weapon-acid-line', 'damage-resistance-acid']],
                ['name' => 'Blue Dragon Ancestry', 'slug' => 'blue-dragonborn', 'features' => ['blue-dragon-ancestry', 'breath-weapon-lightning-line', 'damage-resistance-lightning']],
                ['name' => 'Brass Dragon Ancestry', 'slug' => 'brass-dragonborn', 'features' => ['brass-dragon-ancestry', 'breath-weapon-fire-line', 'damage-resistance-fire']],
                ['name' => 'Bronze Dragon Ancestry', 'slug' => 'bronze-dragonborn', 'features' => ['bronze-dragon-ancestry', 'breath-weapon-lightning-line', 'damage-resistance-lightning']],
                ['name' => 'Copper Dragon Ancestry', 'slug' => 'copper-dragonborn', 'features' => ['copper-dragon-ancestry', 'breath-weapon-acid-line', 'damage-resistance-acid']],
                ['name' => 'Gold Dragon Ancestry', 'slug' => 'gold-dragonborn', 'features' => ['gold-dragon-ancestry', 'breath-weapon-fire-cone', 'damage-resistance-fire']],
                ['name' => 'Green Dragon Ancestry', 'slug' => 'green-dragonborn', 'features' => ['green-dragon-ancestry', 'breath-weapon-poison-cone', 'damage-resistance-poison']],
                ['name' => 'Red Dragon Ancestry', 'slug' => 'red-dragonborn', 'features' => ['red-dragon-ancestry', 'breath-weapon-fire-cone', 'damage-resistance-fire']],
                ['name' => 'Silver Dragon Ancestry', 'slug' => 'silver-dragonborn', 'features' => ['silver-dragon-ancestry', 'breath-weapon-cold-cone', 'damage-resistance-cold']],
                ['name' => 'White Dragon Ancestry', 'slug' => 'white-dragonborn', 'features' => ['white-dragon-ancestry', 'breath-weapon-cold-cone', 'damage-resistance-cold']],
            ],
            'gnome' => [
                ['name' => 'Forest Gnome', 'slug' => 'forest-gnome', 'ability_bonuses' => ['dexterity' => 1], 'features' => ['natural-illusionist', 'speak-with-small-beasts']],
                ['name' => 'Rock Gnome', 'slug' => 'rock-gnome', 'ability_bonuses' => ['constitution' => 1], 'features' => ['artificers-lore', 'tinker']],
            ],
        ];

        RaceSubrace::query()
            ->where('slug', 'dragonborn-traits')
            ->delete();

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
