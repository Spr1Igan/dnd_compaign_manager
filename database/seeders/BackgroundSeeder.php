<?php

namespace Database\Seeders;

use App\Models\Background;
use Illuminate\Database\Seeder;

class BackgroundSeeder extends Seeder
{
    public function run(): void
    {
        $backgrounds = [
            ['name' => 'Прислужник', 'slug' => 'acolyte', 'description' => 'Служитель храма и знаток священных ритуалов.', 'skill_proficiencies' => ['insight', 'religion'], 'tool_proficiencies' => [], 'languages' => ['choose:2'], 'equipment' => ['holy-symbol', 'prayer-book', 'incense-sticks', 'vestments', 'common-clothes', 'belt-pouch'], 'features' => ['shelter-of-the-faithful']],
            ['name' => 'Шарлатан', 'slug' => 'charlatan', 'description' => 'Мастер обмана, масок и чужой доверчивости.', 'skill_proficiencies' => ['deception', 'sleight-of-hand'], 'tool_proficiencies' => ['disguise-kit', 'forgery-kit'], 'languages' => [], 'equipment' => ['fine-clothes', 'disguise-kit', 'con-tools', 'belt-pouch'], 'features' => ['false-identity']],
            ['name' => 'Преступник', 'slug' => 'criminal', 'description' => 'Знаток теневой стороны общества.', 'skill_proficiencies' => ['deception', 'stealth'], 'tool_proficiencies' => ['gaming-set', 'thieves-tools'], 'languages' => [], 'equipment' => ['crowbar', 'dark-common-clothes', 'belt-pouch'], 'features' => ['criminal-contact']],
            ['name' => 'Артист', 'slug' => 'entertainer', 'description' => 'Исполнитель, который умеет владеть вниманием публики.', 'skill_proficiencies' => ['acrobatics', 'performance'], 'tool_proficiencies' => ['disguise-kit', 'musical-instrument'], 'languages' => [], 'equipment' => ['musical-instrument', 'favor-of-admirer', 'costume', 'belt-pouch'], 'features' => ['by-popular-demand']],
            ['name' => 'Народный герой', 'slug' => 'folk-hero', 'description' => 'Защитник простых людей, вышедший из народа.', 'skill_proficiencies' => ['animal-handling', 'survival'], 'tool_proficiencies' => ['artisan-tools', 'vehicles-land'], 'languages' => [], 'equipment' => ['artisan-tools', 'shovel', 'iron-pot', 'common-clothes', 'belt-pouch'], 'features' => ['rustic-hospitality']],
            ['name' => 'Гильдейский ремесленник', 'slug' => 'guild-artisan', 'description' => 'Член ремесленной гильдии, знающий цену труду и связям.', 'skill_proficiencies' => ['insight', 'persuasion'], 'tool_proficiencies' => ['artisan-tools'], 'languages' => ['choose:1'], 'equipment' => ['artisan-tools', 'letter-of-introduction', 'traveler-clothes', 'belt-pouch'], 'features' => ['guild-membership']],
            ['name' => 'Отшельник', 'slug' => 'hermit', 'description' => 'Искатель истины, покоя или откровения.', 'skill_proficiencies' => ['medicine', 'religion'], 'tool_proficiencies' => ['herbalism-kit'], 'languages' => ['choose:1'], 'equipment' => ['scroll-case', 'winter-blanket', 'common-clothes', 'herbalism-kit'], 'features' => ['discovery']],
            ['name' => 'Благородный', 'slug' => 'noble', 'description' => 'Рождённый среди привилегий, обязанностей и родовых связей.', 'skill_proficiencies' => ['history', 'persuasion'], 'tool_proficiencies' => ['gaming-set'], 'languages' => ['choose:1'], 'equipment' => ['fine-clothes', 'signet-ring', 'scroll-of-pedigree', 'purse'], 'features' => ['position-of-privilege']],
            ['name' => 'Чужеземец', 'slug' => 'outlander', 'description' => 'Герой, выросший вдали от городов среди дикой природы.', 'skill_proficiencies' => ['athletics', 'survival'], 'tool_proficiencies' => ['musical-instrument'], 'languages' => ['choose:1'], 'equipment' => ['staff', 'hunting-trap', 'trophy', 'traveler-clothes', 'belt-pouch'], 'features' => ['wanderer']],
            ['name' => 'Мудрец', 'slug' => 'sage', 'description' => 'Исследователь знаний, древних текстов и тайн мира.', 'skill_proficiencies' => ['arcana', 'history'], 'tool_proficiencies' => [], 'languages' => ['choose:2'], 'equipment' => ['bottle-of-ink', 'quill', 'small-knife', 'letter', 'common-clothes', 'belt-pouch'], 'features' => ['researcher']],
            ['name' => 'Моряк', 'slug' => 'sailor', 'description' => 'Путник морей, кораблей и солёного ветра.', 'skill_proficiencies' => ['athletics', 'perception'], 'tool_proficiencies' => ['navigator-tools', 'vehicles-water'], 'languages' => [], 'equipment' => ['belaying-pin', 'silk-rope', 'lucky-charm', 'common-clothes', 'belt-pouch'], 'features' => ['ships-passage']],
            ['name' => 'Солдат', 'slug' => 'soldier', 'description' => 'Ветеран службы, строя и военной дисциплины.', 'skill_proficiencies' => ['athletics', 'intimidation'], 'tool_proficiencies' => ['gaming-set', 'vehicles-land'], 'languages' => [], 'equipment' => ['insignia-of-rank', 'trophy', 'gaming-set', 'common-clothes', 'belt-pouch'], 'features' => ['military-rank']],
            ['name' => 'Беспризорник', 'slug' => 'urchin', 'description' => 'Вырос на улицах и научился выживать среди опасностей города.', 'skill_proficiencies' => ['sleight-of-hand', 'stealth'], 'tool_proficiencies' => ['disguise-kit', 'thieves-tools'], 'languages' => [], 'equipment' => ['small-knife', 'city-map', 'pet-mouse', 'token', 'common-clothes', 'belt-pouch'], 'features' => ['city-secrets']],
        ];

        foreach ($backgrounds as $background) {
            Background::updateOrCreate(['slug' => $background['slug']], $background);
        }
    }
}
