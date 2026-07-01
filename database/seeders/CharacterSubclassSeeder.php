<?php

namespace Database\Seeders;

use App\Models\CharacterClass;
use App\Models\CharacterSubclass;
use Illuminate\Database\Seeder;

class CharacterSubclassSeeder extends Seeder
{
    public function run(): void
    {
        $subclasses = [
            'barbarian' => [
                ['Path of the Berserker', 'path-of-the-berserker', [3 => ['frenzy'], 6 => ['mindless-rage'], 10 => ['intimidating-presence'], 14 => ['retaliation']]],
                ['Path of the Totem Warrior', 'path-of-the-totem-warrior', [3 => ['spirit-seeker', 'totem-spirit'], 6 => ['aspect-of-the-beast'], 10 => ['spirit-walker'], 14 => ['totemic-attunement']]],
            ],
            'bard' => [
                ['College of Lore', 'college-of-lore', [3 => ['bonus-proficiencies', 'cutting-words'], 6 => ['additional-magical-secrets'], 14 => ['peerless-skill']]],
                ['College of Valor', 'college-of-valor', [3 => ['bonus-proficiencies', 'combat-inspiration'], 6 => ['extra-attack'], 14 => ['battle-magic']]],
            ],
            'cleric' => [
                ['Knowledge Domain', 'knowledge-domain', [1 => ['domain-spells-knowledge', 'blessings-of-knowledge'], 2 => ['knowledge-of-the-ages'], 6 => ['read-thoughts'], 8 => ['potent-spellcasting'], 17 => ['visions-of-the-past']]],
                ['Life Domain', 'life-domain', [1 => ['domain-spells-life', 'bonus-proficiency', 'disciple-of-life'], 2 => ['preserve-life'], 6 => ['blessed-healer'], 8 => ['divine-strike'], 17 => ['supreme-healing']]],
                ['Light Domain', 'light-domain', [1 => ['domain-spells-light', 'bonus-cantrip', 'warding-flare'], 2 => ['radiance-of-the-dawn'], 6 => ['improved-flare'], 8 => ['potent-spellcasting'], 17 => ['corona-of-light']]],
                ['Nature Domain', 'nature-domain', [1 => ['domain-spells-nature', 'acolyte-of-nature', 'bonus-proficiency'], 2 => ['charm-animals-and-plants'], 6 => ['dampen-elements'], 8 => ['divine-strike'], 17 => ['master-of-nature']]],
                ['Tempest Domain', 'tempest-domain', [1 => ['domain-spells-tempest', 'bonus-proficiencies', 'wrath-of-the-storm'], 2 => ['destructive-wrath'], 6 => ['thunderbolt-strike'], 8 => ['divine-strike'], 17 => ['stormborn']]],
                ['Trickery Domain', 'trickery-domain', [1 => ['domain-spells-trickery', 'blessing-of-the-trickster'], 2 => ['invoke-duplicity'], 6 => ['cloak-of-shadows'], 8 => ['divine-strike'], 17 => ['improved-duplicity']]],
                ['War Domain', 'war-domain', [1 => ['domain-spells-war', 'bonus-proficiencies', 'war-priest'], 2 => ['guided-strike'], 6 => ['war-gods-blessing'], 8 => ['divine-strike'], 17 => ['avatar-of-battle']]],
            ],
            'druid' => [
                ['Circle of the Land', 'circle-of-the-land', [2 => ['bonus-cantrip', 'natural-recovery'], 3 => ['circle-spells'], 6 => ['lands-stride'], 10 => ['natures-ward'], 14 => ['natures-sanctuary']]],
                ['Circle of the Moon', 'circle-of-the-moon', [2 => ['combat-wild-shape', 'circle-forms'], 6 => ['primal-strike'], 10 => ['elemental-wild-shape'], 14 => ['thousand-forms']]],
            ],
            'fighter' => [
                ['Champion', 'champion', [3 => ['improved-critical'], 7 => ['remarkable-athlete'], 10 => ['additional-fighting-style'], 15 => ['superior-critical'], 18 => ['survivor']]],
                ['Battle Master', 'battle-master', [3 => ['combat-superiority', 'student-of-war'], 7 => ['know-your-enemy'], 10 => ['improved-combat-superiority'], 15 => ['relentless'], 18 => ['improved-combat-superiority-d12']]],
                ['Eldritch Knight', 'eldritch-knight', [3 => ['spellcasting', 'weapon-bond'], 7 => ['war-magic'], 10 => ['eldritch-strike'], 15 => ['arcane-charge'], 18 => ['improved-war-magic']]],
            ],
            'monk' => [
                ['Way of the Open Hand', 'way-of-the-open-hand', [3 => ['open-hand-technique'], 6 => ['wholeness-of-body'], 11 => ['tranquility'], 17 => ['quivering-palm']]],
                ['Way of Shadow', 'way-of-shadow', [3 => ['shadow-arts'], 6 => ['shadow-step'], 11 => ['cloak-of-shadows'], 17 => ['opportunist']]],
                ['Way of the Four Elements', 'way-of-the-four-elements', [3 => ['disciple-of-the-elements'], 6 => ['elemental-disciplines'], 11 => ['elemental-disciplines'], 17 => ['elemental-disciplines']]],
            ],
            'paladin' => [
                ['Oath of Devotion', 'oath-of-devotion', [3 => ['oath-spells-devotion', 'sacred-weapon', 'turn-the-unholy'], 7 => ['aura-of-devotion'], 15 => ['purity-of-spirit'], 20 => ['holy-nimbus']]],
                ['Oath of the Ancients', 'oath-of-the-ancients', [3 => ['oath-spells-ancients', 'natures-wrath', 'turn-the-faithless'], 7 => ['aura-of-warding'], 15 => ['undying-sentinel'], 20 => ['elder-champion']]],
                ['Oath of Vengeance', 'oath-of-vengeance', [3 => ['oath-spells-vengeance', 'abjure-enemy', 'vow-of-enmity'], 7 => ['relentless-avenger'], 15 => ['soul-of-vengeance'], 20 => ['avenging-angel']]],
            ],
            'ranger' => [
                ['Hunter', 'hunter', [3 => ['hunters-prey'], 7 => ['defensive-tactics'], 11 => ['multiattack'], 15 => ['superior-hunters-defense']]],
                ['Beast Master', 'beast-master', [3 => ['rangers-companion'], 7 => ['exceptional-training'], 11 => ['bestial-fury'], 15 => ['share-spells']]],
            ],
            'rogue' => [
                ['Thief', 'thief', [3 => ['fast-hands', 'second-story-work'], 9 => ['supreme-sneak'], 13 => ['use-magic-device'], 17 => ['thiefs-reflexes']]],
                ['Assassin', 'assassin', [3 => ['bonus-proficiencies', 'assassinate'], 9 => ['infiltration-expertise'], 13 => ['impostor'], 17 => ['death-strike']]],
                ['Arcane Trickster', 'arcane-trickster', [3 => ['spellcasting', 'mage-hand-ledgerdemain'], 9 => ['magical-ambush'], 13 => ['versatile-trickster'], 17 => ['spell-thief']]],
            ],
            'sorcerer' => [
                ['Draconic Bloodline', 'draconic-bloodline', [1 => ['dragon-ancestor', 'draconic-resilience'], 6 => ['elemental-affinity'], 14 => ['dragon-wings'], 18 => ['draconic-presence']]],
                ['Wild Magic', 'wild-magic', [1 => ['wild-magic-surge', 'tides-of-chaos'], 6 => ['bend-luck'], 14 => ['controlled-chaos'], 18 => ['spell-bombardment']]],
            ],
            'warlock' => [
                ['The Archfey', 'the-archfey', [1 => ['expanded-spell-list-archfey', 'fey-presence'], 6 => ['misty-escape'], 10 => ['beguiling-defenses'], 14 => ['dark-delirium']]],
                ['The Fiend', 'the-fiend', [1 => ['expanded-spell-list-fiend', 'dark-ones-blessing'], 6 => ['dark-ones-own-luck'], 10 => ['fiendish-resilience'], 14 => ['hurl-through-hell']]],
                ['The Great Old One', 'the-great-old-one', [1 => ['expanded-spell-list-great-old-one', 'awakened-mind'], 6 => ['entropic-ward'], 10 => ['thought-shield'], 14 => ['create-thrall']]],
            ],
            'wizard' => [
                ['School of Abjuration', 'school-of-abjuration', [2 => ['abjuration-savant', 'arcane-ward'], 6 => ['projected-ward'], 10 => ['improved-abjuration'], 14 => ['spell-resistance']]],
                ['School of Conjuration', 'school-of-conjuration', [2 => ['conjuration-savant', 'minor-conjuration'], 6 => ['benign-transportation'], 10 => ['focused-conjuration'], 14 => ['durable-summons']]],
                ['School of Divination', 'school-of-divination', [2 => ['divination-savant', 'portent'], 6 => ['expert-divination'], 10 => ['the-third-eye'], 14 => ['greater-portent']]],
                ['School of Enchantment', 'school-of-enchantment', [2 => ['enchantment-savant', 'hypnotic-gaze'], 6 => ['instinctive-charm'], 10 => ['split-enchantment'], 14 => ['alter-memories']]],
                ['School of Evocation', 'school-of-evocation', [2 => ['evocation-savant', 'sculpt-spells'], 6 => ['potent-cantrip'], 10 => ['empowered-evocation'], 14 => ['overchannel']]],
                ['School of Illusion', 'school-of-illusion', [2 => ['illusion-savant', 'improved-minor-illusion'], 6 => ['malleable-illusions'], 10 => ['illusory-self'], 14 => ['illusory-reality']]],
                ['School of Necromancy', 'school-of-necromancy', [2 => ['necromancy-savant', 'grim-harvest'], 6 => ['undead-thralls'], 10 => ['inured-to-undeath'], 14 => ['command-undead']]],
                ['School of Transmutation', 'school-of-transmutation', [2 => ['transmutation-savant', 'minor-alchemy'], 6 => ['transmuters-stone'], 10 => ['shapechanger'], 14 => ['master-transmuter']]],
            ],
        ];

        foreach ($subclasses as $classSlug => $items) {
            $class = CharacterClass::query()->where('slug', $classSlug)->first();

            if (! $class) {
                continue;
            }

            foreach ($items as [$name, $slug, $featuresByLevel]) {
                CharacterSubclass::updateOrCreate(['slug' => $slug], [
                    'class_id' => $class->id,
                    'name' => $name,
                    'description' => null,
                    'features_by_level' => $featuresByLevel,
                ]);
            }
        }
    }
}
