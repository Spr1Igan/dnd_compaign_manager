<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DmgDataRepository
{
    private const CACHE_TTL = 3600;
    private const CACHE_SCHEMA_VERSION = '23';

    private const GROUPS = [
        'rules' => [
            'title' => 'Правила и справочники',
            'description' => 'Состояния, навыки, языки, опасности, безумие, отдых, путешествия, экономика и другие таблицы для мастера.',
        ],
        'items' => [
            'title' => 'Предметы и сокровища',
            'description' => 'Оружие, доспехи, снаряжение, услуги, транспорт, зелья, магические предметы и сокровища.',
        ],
        'monsters' => [
            'title' => 'Монстры',
            'description' => 'SRD-карточки существ со статами, чувствами, действиями и особенностями.',
        ],
    ];

    private const CATEGORIES = [
        'weapons' => [
            'title' => 'Оружие',
            'description' => 'Обычное оружие: группа, урон, дальность, свойства, цена и вес.',
            'group' => 'items',
            'source' => 'weapons.json',
            'view' => 'weapons',
        ],
        'armor' => [
            'title' => 'Доспехи и щиты',
            'description' => 'Класс доспеха, ограничения Ловкости, требования, помеха Скрытности, цена и вес.',
            'group' => 'items',
            'source' => 'armor.json',
            'view' => 'armor',
        ],
        'adventuring_gear' => [
            'title' => 'Снаряжение',
            'description' => 'Повседневные и приключенческие предметы, стоимость и вес.',
            'group' => 'items',
            'source' => 'adventuring_gear.json',
            'view' => 'equipment',
        ],
        'tools' => [
            'title' => 'Инструменты',
            'description' => 'Ремесленные инструменты, наборы, игровые принадлежности и музыкальные инструменты.',
            'group' => 'items',
            'source' => 'tools.json',
            'view' => 'equipment',
        ],
        'mounts' => [
            'title' => 'Ездовые животные',
            'description' => 'Ездовые и тягловые животные, скорость и переносимый вес.',
            'group' => 'items',
            'source' => 'mounts.json',
            'view' => 'mounts',
        ],
        'vehicles' => [
            'title' => 'Транспорт',
            'description' => 'Сухопутный и водный транспорт, цена, вес и скорость.',
            'group' => 'items',
            'source' => 'vehicles.json',
            'view' => 'equipment',
        ],
        'services' => [
            'title' => 'Услуги',
            'description' => 'Наёмники, жильё, питание, перевозки и прочие услуги.',
            'group' => 'items',
            'source' => 'services.json',
            'view' => 'services',
        ],
        'trade_goods' => [
            'title' => 'Торговые товары',
            'description' => 'Сырьё и ориентировочные цены для торговли.',
            'group' => 'items',
            'source' => 'trade_goods.json',
            'view' => 'services',
        ],
        'potions' => [
            'title' => 'Зелья',
            'description' => 'Основные магические зелья и масла с редкостью, эффектом и ориентиром цены.',
            'group' => 'items',
            'source' => 'potions.json',
            'view' => 'effects',
        ],
        'magic_weapons_armor' => [
            'title' => 'Магическое оружие и доспехи',
            'description' => 'Проверенный русскоязычный список магического оружия, доспехов и щитов.',
            'group' => 'items',
            'source' => 'magic_weapons_and_armor.json',
            'view' => 'effects',
        ],
        'magic_items_srd' => [
            'title' => 'Магические предметы SRD',
            'description' => 'Полная выгрузка магических предметов SRD API. Текст источника сохранён как в наборе данных.',
            'group' => 'items',
            'source' => 'dnd5e_srd_extended/downloaded/magic_items.json',
            'view' => 'effects',
        ],
        'artifacts_srd' => [
            'title' => 'Артефакты SRD',
            'description' => 'Артефакты из SRD API.',
            'group' => 'items',
            'source' => 'dnd5e_srd_extended/downloaded/artifacts.json',
            'view' => 'effects',
        ],
        'properties_currency' => [
            'title' => 'Валюта, свойства и редкости',
            'description' => 'Справочные таблицы валют, свойств оружия и редкостей из каталога предметов.',
            'group' => 'items',
            'source' => 'properties_and_currency.json',
            'view' => 'reference',
            'mode' => 'dict',
        ],
        'gems' => [
            'title' => 'Самоцветы',
            'description' => 'Камни и самоцветы с примерной ценой для сокровищниц и наград.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/gems.json',
            'view' => 'treasure_table',
        ],
        'jewelry' => [
            'title' => 'Украшения',
            'description' => 'Украшения, материалы и стоимость для наград и торговли.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/jewelry.json',
            'view' => 'treasure_table',
        ],
        'art_objects' => [
            'title' => 'Предметы искусства',
            'description' => 'Кубки, статуэтки, картины и другие ценные предметы.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/art_objects.json',
            'view' => 'treasure_table',
        ],
        'books' => [
            'title' => 'Книги',
            'description' => 'Авторские книги-предметы: изучение, эффект, редкость, стоимость и настройка.',
            'group' => 'items',
            'source' => 'dnd5e_books_scrolls_items_ru/books_items.json',
            'view' => 'books',
        ],
        'scrolls' => [
            'title' => 'Свитки',
            'description' => 'Универсальные одноразовые свитки, которые может использовать любое существо, способное их прочитать.',
            'group' => 'items',
            'source' => 'dnd5e_books_scrolls_items_ru/universal_spell_scrolls.json',
            'view' => 'scrolls',
        ],
        'treasure_books' => [
            'title' => 'Книги-сокровища',
            'description' => 'Старые книги и рукописи как ценности для наград и сокровищниц.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/books.json',
            'view' => 'treasure_table',
        ],
        'rare_goods' => [
            'title' => 'Редкие товары',
            'description' => 'Редкое сырьё и товары, которые можно использовать как награду или товарный ресурс.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/rare_goods.json',
            'view' => 'treasure_table',
        ],
        'relics' => [
            'title' => 'Реликвии',
            'description' => 'Сюжетные ценности с крючками для приключений.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/relics.json',
            'view' => 'treasure',
        ],
        'mundane_loot' => [
            'title' => 'Бытовая добыча',
            'description' => 'Небольшие находки, которые могут пригодиться в сцене или стать зацепкой.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/mundane_loot.json',
            'view' => 'treasure_table',
        ],
        'treasure_tables' => [
            'title' => 'Таблицы сокровищ',
            'description' => 'Индивидуальные и кладовые награды по диапазонам опасности.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/treasure_tables.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'coins' => [
            'title' => 'Монеты',
            'description' => 'Валюты D&D и их соотношение.',
            'group' => 'items',
            'source' => 'dnd5e_custom_treasure/coins.json',
            'view' => 'reference',
            'mode' => 'dict',
        ],
        'conditions' => [
            'title' => 'Состояния',
            'description' => 'Состояния персонажей и существ с игровыми эффектами.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/conditions_ru.json',
            'view' => 'rules',
        ],
        'weapon_properties' => [
            'title' => 'Свойства оружия',
            'description' => 'Боеприпасы, лёгкое, тяжёлое, фехтовальное и другие свойства оружия.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/weapon_properties_ru.json',
            'view' => 'rules',
        ],
        'armor_properties' => [
            'title' => 'Типы доспехов',
            'description' => 'Как тип доспеха влияет на формулу КД и модификатор Ловкости.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/armor_properties_ru.json',
            'view' => 'rules',
        ],
        'skills' => [
            'title' => 'Навыки',
            'description' => 'Навыки и характеристики, от которых они зависят.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/skills_ru.json',
            'view' => 'skills',
        ],
        'proficiencies' => [
            'title' => 'Владения SRD',
            'description' => 'Владения из SRD API: инструменты, оружие, доспехи и связи с классами/расами.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/downloaded/proficiencies_api.json',
            'view' => 'reference',
        ],
        'languages' => [
            'title' => 'Языки',
            'description' => 'Стандартные и экзотические языки, письменность и диалекты.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/languages_ru.json',
            'view' => 'reference',
        ],
        'damage_types' => [
            'title' => 'Типы урона',
            'description' => 'Основные типы урона для атак, заклинаний и опасностей.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/damage_types.json',
            'view' => 'reference',
        ],
        'senses' => [
            'title' => 'Чувства',
            'description' => 'Слепое зрение, тёмное зрение, истинное зрение и другие способы восприятия.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/senses.json',
            'view' => 'rules',
        ],
        'creature_sizes' => [
            'title' => 'Размеры существ',
            'description' => 'Размер, занимаемое пространство и типичная кость хитов.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/creature_sizes.json',
            'view' => 'reference',
        ],
        'alignments' => [
            'title' => 'Мировоззрения',
            'description' => 'Список мировоззрений для персонажей и существ.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/alignments.json',
            'view' => 'reference',
        ],
        'challenge_rating' => [
            'title' => 'Показатель опасности',
            'description' => 'ПО, опыт и бонус мастерства существа.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/challenge_rating.json',
            'view' => 'cr',
        ],
        'xp_thresholds' => [
            'title' => 'Пороги опыта',
            'description' => 'Пороги сложности столкновений по уровню персонажа.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/xp_thresholds.json',
            'view' => 'xp',
        ],
        'diseases' => [
            'title' => 'Болезни',
            'description' => 'Заражения, инкубация, эффекты и лечение.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/diseases.json',
            'view' => 'rules',
        ],
        'poisons' => [
            'title' => 'Яды',
            'description' => 'Категории ядов, спасброски и эффекты.',
            'group' => 'items',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/poisons.json',
            'view' => 'rules',
        ],
        'traps' => [
            'title' => 'Ловушки',
            'description' => 'Наши уже структурированные ловушки: срабатывание, обнаружение, обезвреживание и эффект.',
            'group' => 'rules',
            'source' => 'entities/traps.json',
            'source_base' => 'manual',
            'view' => 'rules',
        ],
        'exhaustion' => [
            'title' => 'Истощение',
            'description' => 'Уровни истощения и восстановление.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/exhaustion.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'resting' => [
            'title' => 'Отдых',
            'description' => 'Короткий и продолжительный отдых.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/resting.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'travel' => [
            'title' => 'Путешествия',
            'description' => 'Темп путешествия, сложная местность, марш и навигация.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/travel.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'economy' => [
            'title' => 'Экономика',
            'description' => 'Валюта, расходы на образ жизни и продажа добычи.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/economy.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'crafting' => [
            'title' => 'Создание предметов',
            'description' => 'Правила изготовления обычных и магических предметов.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/crafting.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'encounters' => [
            'title' => 'Столкновения',
            'description' => 'Построение столкновений и множители сложности.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/encounters.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'downtime' => [
            'title' => 'Деятельность между приключениями',
            'description' => 'Ремесло, исследования, обучение и прочие активности между приключениями.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/downtime.json',
            'view' => 'rules',
        ],
        'hiring' => [
            'title' => 'Наёмники',
            'description' => 'Типы наёмников и ориентиры лояльности.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/dnd5e_gm_rules_core_ru/hiring.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'madness_minor' => [
            'title' => 'Кратковременное безумие',
            'description' => 'Краткие эффекты безумия с длительностью и спасброском.',
            'group' => 'rules',
            'source' => 'dnd5e_madness_system_ru/minor_madness.json',
            'view' => 'madness',
        ],
        'madness_major' => [
            'title' => 'Долговременное безумие',
            'description' => 'Более тяжёлые эффекты безумия для сцен давления и ужаса.',
            'group' => 'rules',
            'source' => 'dnd5e_madness_system_ru/major_madness.json',
            'view' => 'madness',
        ],
        'madness_chronic' => [
            'title' => 'Хроническое безумие',
            'description' => 'Длительные состояния, требующие лечения или сюжетного разрешения.',
            'group' => 'rules',
            'source' => 'dnd5e_madness_system_ru/chronic_madness.json',
            'view' => 'madness',
        ],
        'madness_triggers' => [
            'title' => 'Триггеры безумия',
            'description' => 'Ситуации, которые могут вызвать проверки против безумия.',
            'group' => 'rules',
            'source' => 'dnd5e_madness_system_ru/triggers.json',
            'view' => 'rules',
        ],
        'madness_treatments' => [
            'title' => 'Лечение безумия',
            'description' => 'Способы подавления или снятия эффектов безумия.',
            'group' => 'rules',
            'source' => 'dnd5e_madness_system_ru/treatments.json',
            'view' => 'rules',
        ],
        'madness_rules' => [
            'title' => 'Правила безумия',
            'description' => 'Базовые правила применения, наложения и безопасного использования системы безумия.',
            'group' => 'rules',
            'source' => 'dnd5e_madness_system_ru/rules.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'madness_tables' => [
            'title' => 'Таблицы безумия',
            'description' => 'Таблицы бросков и эскалации для системы безумия.',
            'group' => 'rules',
            'source' => 'dnd5e_madness_system_ru/tables.json',
            'view' => 'rules',
            'mode' => 'dict',
        ],
        'spells' => [
            'title' => 'Заклинания SRD',
            'description' => 'Заклинания из SRD API. Названия и текст источника сохранены как в наборе данных.',
            'group' => 'rules',
            'source' => 'dnd5e_srd_extended/downloaded/spells.json',
            'view' => 'spells',
        ],
        'monsters' => [
            'title' => 'Монстры SRD',
            'description' => 'Существа из SRD API: КД, хиты, скорость, ПО, чувства, особенности и действия.',
            'group' => 'monsters',
            'source' => 'dnd5e_srd_extended/downloaded/monsters.json',
            'view' => 'monsters',
        ],
    ];

    public function basePath(): string
    {
        return base_path('some_data/dnd5e_equipment_ru');
    }

    public function manualBasePath(): string
    {
        return base_path('database/dnd_data/manual');
    }

    public function exists(): bool
    {
        return is_dir($this->basePath());
    }

    public function metadata(): array
    {
        return $this->readJson('metadata.json', []);
    }

    public function categories(): array
    {
        return collect(self::CATEGORIES)
            ->map(fn (array $category, string $slug): array => [
                ...$category,
                'slug' => $slug,
                'count' => count($this->entries($slug)),
            ])
            ->all();
    }

    public function groups(): array
    {
        $categories = collect($this->categories());

        return collect(self::GROUPS)
            ->map(function (array $group, string $slug) use ($categories): array {
                $groupCategories = $categories
                    ->filter(fn (array $category): bool => ($category['group'] ?? null) === $slug)
                    ->values()
                    ->all();

                return [
                    ...$group,
                    'slug' => $slug,
                    'count' => collect($groupCategories)->sum('count'),
                    'categories' => $groupCategories,
                ];
            })
            ->all();
    }

    public function category(string $slug): ?array
    {
        if (! isset(self::CATEGORIES[$slug])) {
            return null;
        }

        return [
            ...self::CATEGORIES[$slug],
            'slug' => $slug,
            'count' => count($this->entries($slug)),
        ];
    }

    public function entries(string $slug): array
    {
        if (! isset(self::CATEGORIES[$slug])) {
            return [];
        }

        return Cache::remember($this->cacheKey("entries.{$slug}"), self::CACHE_TTL, function () use ($slug): array {
            $category = self::CATEGORIES[$slug];
            $items = $this->rawEntries($category, $slug);

            return collect($items)
                ->values()
                ->map(fn (array $item, int $index): array => $this->normalizeEntry($item, $slug, $index, $category))
                ->sortBy(fn (array $entry): string => Str::lower($entry['name']))
                ->values()
                ->map(function (array $entry, int $index): array {
                    $entry['index'] = $index;

                    return $entry;
                })
                ->all();
        });
    }

    public function entry(string $slug, int $index): ?array
    {
        return $this->entries($slug)[$index] ?? null;
    }

    public function search(?string $query, ?string $category = null): array
    {
        $query = trim((string) $query);

        if ($query === '') {
            return [];
        }

        $categorySlugs = $category && isset(self::CATEGORIES[$category])
            ? [$category]
            : array_keys(self::CATEGORIES);

        return collect($categorySlugs)
            ->flatMap(fn (string $slug): array => $this->entries($slug))
            ->filter(function (array $entry) use ($query): bool {
                $haystack = mb_strtolower(implode(' ', [
                    $entry['name'],
                    $entry['english_name'],
                    $entry['description'],
                    $entry['type'],
                    $entry['item_group'],
                    $entry['rarity'],
                    implode(' ', $entry['properties']),
                    implode(' ', $entry['tags']),
                    collect($entry['stats'])->pluck('value')->implode(' '),
                    collect($entry['sections'])->pluck('text')->implode(' '),
                ]));

                return str_contains($haystack, mb_strtolower($query));
            })
            ->take(100)
            ->values()
            ->all();
    }

    public function chapters(): array
    {
        return [];
    }

    public function chapter(string $id): ?array
    {
        return null;
    }

    public function tableGroups(): array
    {
        return [];
    }

    private function rawEntries(array $category, string $slug): array
    {
        $source = (string) ($category['source'] ?? '');
        $base = ($category['source_base'] ?? null) === 'manual'
            ? $this->manualBasePath()
            : $this->basePath();

        $data = $this->readJsonFromBase($base, $source, []);

        if (! is_array($data)) {
            return [];
        }

        if (($category['mode'] ?? null) === 'dict' || ! array_is_list($data)) {
            return collect($data)
                ->map(fn (mixed $value, string|int $key): array => [
                    'id' => (string) $key,
                    ...is_array($value) && ! array_is_list($value) ? $value : ['value' => $value],
                    'name_ru' => $this->dictionaryTitle((string) $key),
                ])
                ->values()
                ->all();
        }

        return collect($data)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->values()
            ->all();
    }

    private function normalizeEntry(array $item, string $slug, int $index, array $category): array
    {
        $name = $this->entryName($item, $slug, $index);
        $englishName = $this->firstText($item, ['name_en']);
        $description = $this->descriptionText($item, $slug);
        $stats = $this->statsFor($item, $slug);
        $sections = $this->sectionsFor($item, $slug);
        $properties = $this->propertiesFor($item);
        $tags = $this->normalizeTextList($item['tags'] ?? []);
        $type = $this->translateTerm($this->firstText($item, ['type', 'item_type', 'subtype', 'category', 'severity']));
        $itemGroup = $this->translateTerm($this->firstText($item, ['category', 'weapon_group', 'armor_group', 'subtype', 'item_type']));

        return [
            'index' => $index,
            'id' => $this->flattenText($item['id'] ?? $item['index'] ?? (string) $index),
            'category_slug' => $slug,
            'category_title' => $category['title'],
            'category_view' => $category['view'] ?? 'cards',
            'name' => $name !== '' ? $name : 'Запись '.($index + 1),
            'english_name' => $englishName,
            'type' => $type,
            'item_group' => $itemGroup,
            'cost' => $this->costText($item['cost'] ?? $item['cost_gp'] ?? ''),
            'weight' => $item['weight_lb'] ?? null,
            'damage' => $this->damageText($item['damage'] ?? ''),
            'damage_type' => $this->translateTerm($this->flattenText(is_array($item['damage'] ?? null) ? ($item['damage']['type'] ?? '') : ($item['damage_type'] ?? ''))),
            'range' => $this->rangeText($item['range_ft'] ?? $item['range'] ?? ''),
            'properties' => $properties,
            'armor_class_formula' => $this->armorClassText($item),
            'dexterity_bonus' => $this->dexterityText($item['dex_bonus'] ?? $item['dexterity_bonus'] ?? ''),
            'requirement' => $this->requirementText($item),
            'stealth_disadvantage' => $item['stealth_disadvantage'] ?? null,
            'rarity' => $this->rarityText($item['rarity'] ?? ''),
            'attunement' => $item['attunement'] ?? $item['requires_attunement'] ?? null,
            'consumable' => $item['consumable'] ?? null,
            'charges' => $item['charges'] ?? null,
            'activation' => $this->activationText($item['activation'] ?? null),
            'area' => $this->areaText($item['area'] ?? null),
            'duration' => $this->flattenText($item['duration'] ?? ''),
            'concentration' => $item['concentration'] ?? null,
            'usable_by' => $this->flattenText($item['usable_by'] ?? ''),
            'suggested_price' => $this->suggestedPriceText($item['suggested_price_gp'] ?? null),
            'unit' => $this->flattenText($item['unit'] ?? ''),
            'speed_ft' => $item['speed_ft'] ?? null,
            'carrying_capacity_lb' => $item['carrying_capacity_lb'] ?? null,
            'value_gp' => $item['value_gp'] ?? null,
            'saving_throw' => $this->savingThrowText($item['save'] ?? $item['saving_throw'] ?? ''),
            'dc' => $this->dcValue($item['save'] ?? $item['saving_throw'] ?? $item['dc'] ?? null),
            'armor_class' => $this->monsterArmorClass($item),
            'hit_points' => $this->monsterHitPoints($item),
            'speed' => $this->speedText($item['speed'] ?? ''),
            'size' => $this->translateTerm($this->flattenText($item['size'] ?? '')),
            'alignment' => $this->translateTerm($this->flattenText($item['alignment'] ?? '')),
            'challenge_rating' => $this->challengeRatingText($item),
            'xp' => $item['xp'] ?? null,
            'abilities' => $this->monsterAbilities($item),
            'skills' => $this->monsterProficiencies($item, 'Skill:'),
            'saves' => $this->monsterProficiencies($item, 'Saving Throw:'),
            'senses' => $this->monsterSenses($item),
            'languages' => $this->monsterLanguages($item),
            'traits' => $this->namedDescriptions($item['special_abilities'] ?? $item['traits'] ?? []),
            'actions' => array_merge(
                $this->namedDescriptions($item['actions'] ?? []),
                $this->namedDescriptions($item['legendary_actions'] ?? [])
            ),
            'stats' => $stats,
            'sections' => $sections,
            'description' => $description,
            'effect' => $this->normalizeTextList($item['effect'] ?? $item['effect_ru'] ?? []),
            'tags' => $tags,
            'is_manual' => ($category['source_base'] ?? null) === 'manual',
            'source_path' => $category['source'] ?? '',
            'excerpt' => Str::limit($description !== '' ? $description : collect($sections)->pluck('text')->implode(' '), 260),
        ];
    }

    private function statsFor(array $item, string $slug): array
    {
        $stats = [];
        $add = function (string $label, mixed $value) use (&$stats): void {
            $text = $this->statValue($value);

            if ($text !== '') {
                $stats[] = ['label' => $label, 'value' => $text];
            }
        };

        $add('Тип', $this->translateTerm($item['type'] ?? $item['item_type'] ?? $item['subtype'] ?? $item['category'] ?? $item['severity'] ?? null));
        $add('Группа', is_array($item['equipment_category'] ?? null) ? $this->translateTerm($item['equipment_category']['name'] ?? null) : null);
        $add('Редкость', $this->rarityText($item['rarity'] ?? null));
        $add('Характеристика', isset($item['ability']) ? $this->abilityName($item['ability']) : null);
        $add('Письменность', $item['script'] ?? null);
        $add('Пространство', isset($item['space_ft']) ? $item['space_ft'].' фт.' : null);
        $add('Кость хитов', $item['typical_hit_die'] ?? null);
        $add('ПО', $item['cr'] ?? null);
        $add('Стоимость', $this->costText($item['cost'] ?? $item['cost_gp'] ?? ''));
        $add('Цена', isset($item['value_gp']) ? $item['value_gp'].' зм' : null);
        $add('Стоимость в золотых', isset($item['gp_value']) ? $item['gp_value'] : null);
        $add('Вес', isset($item['weight_lb']) ? $item['weight_lb'].' фнт.' : null);
        $add('Единица', $item['unit'] ?? null);
        $add('Урон', $this->damageText($item['damage'] ?? ''));
        $add('Тип урона', is_array($item['damage'] ?? null) ? $this->translateTerm($item['damage']['type'] ?? null) : null);
        $add('Дальность', $this->rangeText($item['range_ft'] ?? $item['range'] ?? ''));
        $add('КД', $this->armorClassText($item));
        $add('КД', $this->monsterArmorClass($item));
        $add('Хиты', $this->monsterHitPoints($item));
        $add('Ловкость', $this->dexterityText($item['dex_bonus'] ?? $item['dexterity_bonus'] ?? ''));
        $add('Требование', $this->requirementText($item));
        $add('Скрытность', array_key_exists('stealth_disadvantage', $item) ? ($item['stealth_disadvantage'] ? 'Помеха' : 'Без помехи') : null);
        $add('Настройка', array_key_exists('attunement', $item) || array_key_exists('requires_attunement', $item) ? (($item['attunement'] ?? $item['requires_attunement']) ? 'Требуется' : 'Не требуется') : null);
        $add('Расходуемое', array_key_exists('consumable', $item) ? ($item['consumable'] ? 'Да' : 'Нет') : null);
        $add('Заряды', $item['charges'] ?? null);
        $add('Активация', $this->activationText($item['activation'] ?? null));
        $add('Кто может использовать', $item['usable_by'] ?? null);
        $add('Область', $this->areaText($item['area'] ?? null));
        $add('Ориентир цены', $this->suggestedPriceText($item['suggested_price_gp'] ?? null));
        $add('Скорость', isset($item['speed_ft']) ? $item['speed_ft'].' фт.' : ($this->speedText($item['speed'] ?? '') ?: null));
        $add('Груз', isset($item['carrying_capacity_lb']) ? $item['carrying_capacity_lb'].' фнт.' : null);
        $add('Спасбросок', $this->savingThrowText($item['save'] ?? $item['saving_throw'] ?? ''));
        $add('Сложность', $this->dcValue($item['save'] ?? $item['saving_throw'] ?? $item['dc'] ?? null) ? 'Сл '.$this->dcValue($item['save'] ?? $item['saving_throw'] ?? $item['dc'] ?? null) : null);
        $add('Уровень', isset($item['level']) ? ($item['level'] === 0 ? 'Заговор' : $item['level']) : null);
        $add('Школа', is_array($item['school'] ?? null) ? ($item['school']['name'] ?? '') : ($item['school'] ?? null));
        $add('Время накладывания', $item['casting_time'] ?? null);
        $add('Длительность', $item['duration'] ?? null);
        $add('Концентрация', array_key_exists('concentration', $item) ? ($item['concentration'] ? 'Да' : 'Нет') : null);
        $add('Ритуал', array_key_exists('ritual', $item) ? ($item['ritual'] ? 'Да' : 'Нет') : null);
        $add('ПО', $item['challenge_rating'] ?? null);
        $add('Опыт', isset($item['xp']) ? $item['xp'] : null);
        $add('Бонус мастерства', isset($item['proficiency_bonus']) ? '+'.$item['proficiency_bonus'] : null);
        $add('Лёгкая', isset($item['easy']) ? $item['easy'] : null);
        $add('Средняя', isset($item['medium']) ? $item['medium'] : null);
        $add('Сложная', isset($item['hard']) ? $item['hard'] : null);
        $add('Смертельная', isset($item['deadly']) ? $item['deadly'] : null);

        return $stats;
    }

    private function sectionsFor(array $item, string $slug): array
    {
        $sections = [];
        $known = [
            'id', 'index', 'name', 'name_ru', 'name_en', 'category', 'type', 'item_type', 'subtype', 'severity',
            'cost', 'cost_gp', 'weight_lb', 'gp_value',
            'equipment_category', 'rarity',
            'damage', 'range_ft', 'properties', 'base_ac', 'dex_bonus', 'stealth_disadvantage',
            'strength_requirement', 'ac_bonus', 'rarity', 'attunement', 'requires_attunement', 'attunement_requirement',
            'consumable', 'charges', 'destroyed_on_use',
            'suggested_price_gp', 'unit', 'speed_ft', 'carrying_capacity_lb', 'value_gp',
            'tags', 'save', 'saving_throw', 'dc', 'level', 'school', 'casting_time', 'duration',
            'concentration', 'ritual', 'challenge_rating', 'xp', 'proficiency_bonus',
            'armor_class', 'hit_points', 'hit_dice', 'hit_points_roll', 'speed', 'size',
            'alignment', 'strength', 'dexterity', 'constitution', 'intelligence', 'wisdom',
            'charisma', 'proficiencies', 'senses', 'languages', 'special_abilities', 'actions', 'legendary_actions',
            'url', 'image', 'updated_at', 'variants', 'variant', 'reference',
        ];

        $labels = [
            'description_ru' => 'Описание',
            'description' => 'Описание',
            'desc' => 'Описание',
            'effect' => 'Эффект',
            'effect_ru' => 'Эффект',
            'effects' => 'Эффекты',
            'knowledge_gained' => 'Что даёт изучение',
            'activation' => 'Использование',
            'area' => 'Область',
            'usable_by' => 'Кто может использовать',
            'treatment' => 'Лечение',
            'incubation' => 'Инкубация',
            'outcome' => 'Результат',
            'notes' => 'Заметки',
            'hook' => 'Сюжетный крючок',
            'use' => 'Использование',
            'subject' => 'Тема',
            'material' => 'Материал',
            'condition' => 'Состояние',
            'roleplay_hint' => 'Подсказка для отыгрыша',
            'removal' => 'Снятие эффекта',
            'higher_level' => 'На высоких уровнях',
            'components' => 'Компоненты',
            'classes' => 'Классы',
            'subclasses' => 'Подклассы',
            'damage_vulnerabilities' => 'Уязвимости к урону',
            'damage_resistances' => 'Сопротивления урону',
            'damage_immunities' => 'Иммунитеты к урону',
            'condition_immunities' => 'Иммунитеты к состояниям',
            'value' => 'Данные',
        ];

        foreach ($labels as $key => $label) {
            if (array_key_exists($key, $item)) {
                if (in_array($key, ['description_ru', 'description', 'desc', 'effect', 'effect_ru', 'effects', 'outcome', 'hook', 'use'], true)) {
                    continue;
                }

                $text = $this->sectionText($item[$key]);

                if ($text !== '') {
                    $sections[] = ['key' => $key, 'title' => $label, 'text' => $text];
                }
            }
        }

        foreach ($item as $key => $value) {
            if (in_array($key, $known, true) || isset($labels[$key])) {
                continue;
            }

            $text = $this->sectionText($value);

            if ($text !== '') {
                $sections[] = [
                    'key' => (string) $key,
                    'title' => $this->dictionaryTitle((string) $key),
                    'text' => $text,
                ];
            }
        }

        return $sections;
    }

    private function descriptionText(array $item, string $slug): string
    {
        if (in_array($slug, ['magic_items_srd', 'artifacts_srd'], true)) {
            $translated = $this->magicItemById($this->flattenText($item['index'] ?? $item['id'] ?? ''));
            $effect = $this->sectionText($translated['effect'] ?? '');

            if ($effect !== '') {
                return $effect;
            }

            $desc = $this->sectionText($item['desc'] ?? '');

            if ($desc !== '') {
                return $desc;
            }
        }

        foreach (['description_ru', 'description', 'effect_ru', 'effect', 'effects', 'desc', 'outcome', 'hook', 'use'] as $key) {
            if (array_key_exists($key, $item)) {
                $text = $this->sectionText($item[$key]);

                if ($text !== '') {
                    return $text;
                }
            }
        }

        if (isset($item['cost']) || isset($item['value_gp']) || isset($item['damage']) || isset($item['base_ac'])) {
            return collect($this->statsFor($item, $slug))
                ->map(fn (array $stat): string => $stat['label'].': '.$stat['value'])
                ->implode('. ');
        }

        return '';
    }

    private function propertiesFor(array $item): array
    {
        if (isset($item['properties'])) {
            return $this->normalizeTextList($item['properties']);
        }

        if (isset($item['dialects'])) {
            return $this->normalizeTextList($item['dialects']);
        }

        return [];
    }

    private function costText(mixed $cost): string
    {
        if (is_array($cost)) {
            $amount = $cost['amount'] ?? null;
            $unit = $this->currencyUnit($cost['unit'] ?? '');

            return $amount !== null && $unit !== '' ? $amount.' '.$unit : '';
        }

        $text = $this->flattenText($cost);

        return is_numeric($text) ? $text.' зм' : $text;
    }

    private function currencyUnit(mixed $unit): string
    {
        return match ($this->flattenText($unit)) {
            'cp' => 'мм',
            'sp' => 'см',
            'ep' => 'эм',
            'gp' => 'зм',
            'pp' => 'пм',
            default => $this->flattenText($unit),
        };
    }

    private function damageText(mixed $damage): string
    {
        if (is_array($damage)) {
            return $this->flattenText($damage['dice'] ?? '');
        }

        return $this->flattenText($damage);
    }

    private function rangeText(mixed $range): string
    {
        if (is_array($range)) {
            $normal = $range['normal'] ?? null;
            $long = $range['long'] ?? null;

            return $normal !== null && $long !== null ? $normal.'/'.$long.' фт.' : '';
        }

        $text = $this->flattenText($range);

        return is_numeric($text) ? $text.' фт.' : $text;
    }

    private function activationText(mixed $activation): string
    {
        if (! is_array($activation)) {
            return $this->translateTerm($this->flattenText($activation));
        }

        $type = $this->flattenText($activation['type'] ?? '');
        $text = match ($type) {
            'study' => 'Изучение',
            'action' => 'Действие',
            'bonus_action' => 'Бонусное действие',
            'reaction' => 'Реакция',
            default => $this->translateTerm($type),
        };

        if (isset($activation['study_time_hours'])) {
            $text .= ' '.$activation['study_time_hours'].' ч.';
        }

        if (! empty($activation['components']) && is_array($activation['components'])) {
            $text .= ', компоненты: '.collect($activation['components'])
                ->map(fn (mixed $component): string => $this->translateTerm($component))
                ->filter()
                ->implode(', ');
        }

        if (array_key_exists('material_components', $activation)) {
            $text .= ', материальные: '.($activation['material_components'] ? 'да' : 'нет');
        }

        return trim($text, ', ');
    }

    private function areaText(mixed $area): string
    {
        if (! is_array($area)) {
            return $this->translateTerm($this->flattenText($area));
        }

        return match ($this->flattenText($area['shape'] ?? '')) {
            'line' => 'Линия '.($area['length_ft'] ?? '?').' x '.($area['width_ft'] ?? '?').' фт.',
            'cube' => 'Куб '.($area['size_ft'] ?? '?').' фт.',
            'sphere' => 'Сфера радиусом '.($area['radius_ft'] ?? '?').' фт.',
            'single_target' => 'Одна цель',
            'surface' => 'Поверхность',
            'self' => 'На себя',
            'targets' => 'Несколько целей',
            'bridge' => 'Мост',
            default => $this->sectionText($area),
        };
    }

    private function armorClassText(array $item): string
    {
        if (isset($item['ac_formula'])) {
            return $this->flattenText($item['ac_formula']);
        }

        if (isset($item['ac_bonus'])) {
            return '+'.$item['ac_bonus'];
        }

        $base = $item['base_ac'] ?? $item['base_armor_class'] ?? null;

        if ($base === null) {
            return '';
        }

        return match ($this->flattenText($item['dex_bonus'] ?? $item['dexterity_bonus'] ?? '')) {
            'полный' => $base.' + модификатор Ловкости',
            'макс +2', 'максимум +2' => $base.' + модификатор Ловкости, максимум +2',
            'нет', 'не учитывается' => (string) $base,
            default => (string) $base,
        };
    }

    private function dexterityText(mixed $value): string
    {
        return match ($this->flattenText($value)) {
            'полный' => 'полный модификатор',
            'макс +2', 'максимум +2' => 'модификатор, максимум +2',
            'нет', 'не учитывается' => 'не учитывается',
            default => $this->flattenText($value),
        };
    }

    private function requirementText(array $item): string
    {
        if (isset($item['strength_requirement'])) {
            return 'Сила '.$item['strength_requirement'];
        }

        return $this->flattenText($item['requirement'] ?? '');
    }

    private function suggestedPriceText(mixed $price): string
    {
        if (! is_array($price)) {
            return '';
        }

        $min = $price['min'] ?? null;
        $max = $price['max'] ?? null;

        if ($min !== null && $max !== null) {
            return $min === $max ? $min.' зм' : $min.'-'.$max.' зм';
        }

        return '';
    }

    private function savingThrowText(mixed $save): string
    {
        if (is_array($save)) {
            $ability = $this->flattenText($save['ability'] ?? '');
            $dc = $save['dc'] ?? null;

            return trim($this->abilityName($ability).($dc ? ' Сл '.$dc : ''));
        }

        return $this->flattenText($save);
    }

    private function dcValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $value['dc'] ?? null;
        }

        return is_numeric($value) ? $value : null;
    }

    private function monsterArmorClass(array $item): string
    {
        $armorClass = $item['armor_class'] ?? null;

        if (is_array($armorClass)) {
            return collect($armorClass)
                ->map(fn (mixed $part): string => is_array($part)
                    ? trim(($part['value'] ?? '').' '.$this->translateTerm($part['type'] ?? ''))
                    : $this->translateEnglishPhrase($this->flattenText($part)))
                ->filter()
                ->implode(', ');
        }

        return $this->flattenText($armorClass);
    }

    private function monsterHitPoints(array $item): string
    {
        if (! isset($item['hit_points'])) {
            return '';
        }

        $roll = $this->flattenText($item['hit_points_roll'] ?? $item['hit_dice'] ?? '');

        return trim($item['hit_points'].($roll !== '' ? ' ('.$roll.')' : ''));
    }

    private function speedText(mixed $speed): string
    {
        if (is_array($speed)) {
            return collect($speed)
                ->map(function (mixed $value, string|int $key): string {
                    if ((string) $key === 'hover') {
                        return $value ? 'парение' : '';
                    }

                    return $this->dictionaryTitle((string) $key).': '.$this->translateTerm($this->flattenText($value));
                })
                ->filter()
                ->implode(', ');
        }

        return $this->flattenText($speed);
    }

    private function challengeRatingText(array $item): string
    {
        if (! isset($item['challenge_rating'])) {
            return '';
        }

        return $this->flattenText($item['challenge_rating']).(isset($item['xp']) ? ' ('.$item['xp'].' опыта)' : '');
    }

    private function monsterAbilities(array $item): array
    {
        $abilities = [
            'strength' => 'Сила',
            'dexterity' => 'Ловкость',
            'constitution' => 'Телосложение',
            'intelligence' => 'Интеллект',
            'wisdom' => 'Мудрость',
            'charisma' => 'Харизма',
        ];

        return collect($abilities)
            ->map(fn (string $title, string $key): ?array => array_key_exists($key, $item)
                ? ['key' => $key, 'title' => $title, 'value' => $item[$key]]
                : null)
            ->filter()
            ->values()
            ->all();
    }

    private function monsterProficiencies(array $item, string $prefix): array
    {
        if (! is_array($item['proficiencies'] ?? null)) {
            return [];
        }

        return collect($item['proficiencies'])
            ->filter(fn (mixed $part): bool => is_array($part) && str_starts_with($part['proficiency']['name'] ?? '', $prefix))
            ->map(fn (array $part): array => [
                'name' => $this->translateEnglishPhrase(trim(str_replace($prefix, '', $part['proficiency']['name'] ?? ''))),
                'value' => $part['value'] ?? null,
            ])
            ->values()
            ->all();
    }

    private function monsterSenses(array $item): array
    {
        if (! is_array($item['senses'] ?? null)) {
            return [];
        }

        return collect($item['senses'])
            ->map(fn (mixed $value, string|int $name): array => [
                'name' => $this->dictionaryTitle((string) $name),
                'value' => $this->translateEnglishPhrase($this->flattenText($value)),
            ])
            ->values()
            ->all();
    }

    private function monsterLanguages(array $item): array
    {
        $languages = $item['languages'] ?? '';

        if (is_array($languages)) {
            return $this->normalizeTextList($languages);
        }

        return $this->flattenText($languages) !== '' ? [$this->translateEnglishPhrase($this->flattenText($languages))] : [];
    }

    private function namedDescriptions(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(fn (array $item): array => [
                'name' => $this->translateEnglishPhrase($this->flattenText($item['name'] ?? '')),
                'description' => $this->sectionText($item['desc'] ?? $item['description'] ?? ''),
            ])
            ->filter(fn (array $item): bool => $item['name'] !== '' || $item['description'] !== '')
            ->values()
            ->all();
    }

    private function sectionText(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        }

        if (is_array($value)) {
            if (array_key_exists('name', $value) && count(array_filter(array_keys($value), 'is_string')) <= 3) {
                return $this->translateTerm($this->flattenText($value['name']));
            }

            if (array_is_list($value)) {
                return collect($value)
                    ->map(fn (mixed $part): string => $this->sectionText($part))
                    ->filter()
                    ->implode("\n");
            }

            return collect($value)
                ->reject(fn (mixed $part, string|int $key): bool => in_array((string) $key, ['url', 'image', 'updated_at', 'index'], true))
                ->map(fn (mixed $part, string|int $key): string => $this->dictionaryTitle((string) $key).': '.$this->sectionText($part))
                ->filter(fn (string $part): bool => trim($part) !== '')
                ->implode("\n");
        }

        return $this->translateEnglishPhrase($this->translateTerm($this->flattenText($value)));
    }

    private function statValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        }

        if (is_array($value)) {
            if (array_key_exists('name', $value) && count(array_filter(array_keys($value), 'is_string')) <= 3) {
                return $this->translateTerm($this->flattenText($value['name']));
            }

            return $this->sectionText($value);
        }

        return $this->translateTerm($this->flattenText($value));
    }

    private function normalizeTextList(mixed $value): array
    {
        if (! is_array($value)) {
            $value = $value ? [$value] : [];
        }

        return collect($value)
            ->map(fn (mixed $part): string => $this->translateEnglishPhrase($this->flattenText($part)))
            ->filter()
            ->values()
            ->all();
    }

    private function flattenText(mixed $value): string
    {
        if (is_array($value)) {
            $value = implode(' ', array_map(fn (mixed $part): string => $this->flattenText($part), $value));
        }

        return trim((string) preg_replace('/\s+/u', ' ', (string) $value));
    }

    private function firstText(array $item, array $keys): string
    {
        foreach ($keys as $key) {
            $value = $this->flattenText($item[$key] ?? '');

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function entryName(array $item, string $slug, int $index): string
    {
        $name = $this->firstText($item, ['name_ru', 'name', 'title', 'id', 'index']);

        if ($name !== '') {
            if ($slug === 'proficiencies') {
                return $this->proficiencyName($item, $name);
            }

            if ($slug === 'monsters') {
                return $this->monsterName($name);
            }

            if (in_array($slug, ['magic_items_srd', 'artifacts_srd'], true)) {
                $mapped = $this->magicItemNameById($this->flattenText($item['index'] ?? $item['id'] ?? ''));

                if ($mapped !== '') {
                    return $mapped;
                }

                return $this->magicSrdName($name);
            }

            return $this->translatedEntryName($name);
        }

        return match ($slug) {
            'challenge_rating' => 'ПО '.($item['cr'] ?? $index + 1),
            'xp_thresholds' => 'Уровень '.($item['level'] ?? $index + 1),
            default => 'Запись '.($index + 1),
        };
    }

    private function rarityText(mixed $rarity): string
    {
        if (is_array($rarity)) {
            return $this->translateTerm($this->flattenText($rarity['name'] ?? ''));
        }

        return $this->translateTerm($this->flattenText($rarity));
    }

    private function proficiencyName(array $item, string $fallback): string
    {
        $referenceId = $this->flattenText($item['reference']['index'] ?? '');
        $mapped = $referenceId !== '' ? $this->equipmentNameById($referenceId) : '';

        return $mapped !== '' ? $mapped : $this->translatedEntryName($fallback);
    }

    private function magicItemNameById(string $id): string
    {
        $item = $this->magicItemById($id);

        return $this->flattenText($item['name_ru'] ?? '');
    }

    private function magicItemById(string $id): array
    {
        static $map = null;

        if ($map === null) {
            $map = [];

            foreach (['magic_weapons_and_armor.json', 'potions.json'] as $file) {
                $items = $this->readJson($file, []);

                if (! is_array($items)) {
                    continue;
                }

                foreach ($items as $item) {
                    if (is_array($item) && isset($item['id'])) {
                        $map[(string) $item['id']] = $item;
                    }
                }
            }
        }

        return $map[$id] ?? [];
    }

    private function magicSrdName(string $name): string
    {
        $direct = [
            'Adamantine Armor' => 'Адамантиновый доспех',
            'Ammunition, +1, +2, or +3' => 'Боеприпасы, +1, +2 или +3',
            'Amulet of the Planes' => 'Амулет планов',
            'Anchor Feather Token' => 'Перьевой жетон якоря',
            'Apparatus of the Crab' => 'Аппарат краба',
            'Arrow of Slaying' => 'Стрела убийства',
            'Air Elemental Gem' => 'Самоцвет воздушного элементаля',
            'Alchemy Jug' => 'Алхимический кувшин',
            'Ammunition, +1' => 'Боеприпас, +1',
            'Ammunition, +2' => 'Боеприпас, +2',
            'Ammunition, +3' => 'Боеприпас, +3',
            'Amulet of Health' => 'Амулет здоровья',
            'Amulet of Proof against Detection and Location' => 'Амулет защиты от обнаружения и определения местоположения',
            'Animated Shield' => 'Оживлённый щит',
            'Armor, +1, +2, or +3' => 'Доспех, +1, +2 или +3',
            'Armor, +1' => 'Доспех, +1',
            'Armor, +2' => 'Доспех, +2',
            'Armor, +3' => 'Доспех, +3',
            'Armor of Invulnerability' => 'Доспех неуязвимости',
            'Armor of Resistance' => 'Доспех сопротивления',
            'Armor of Vulnerability' => 'Доспех уязвимости',
            'Arrow-Catching Shield' => 'Щит ловли стрел',
            'Bag of Beans' => 'Сумка бобов',
            'Bag of Devouring' => 'Сумка пожирания',
            'Bag of Holding' => 'Сумка хранения',
            'Bag of Tricks' => 'Сумка фокусов',
            'Gray Bag of Tricks' => 'Серая сумка фокусов',
            'Rust Bag of Tricks' => 'Рыжая сумка фокусов',
            'Tan Bag of Tricks' => 'Жёлто-коричневая сумка фокусов',
            'Bead of Force' => 'Бусина силового поля',
            'Belt of Cloud Giant Strength' => 'Пояс силы облачного великана',
            'Belt of Dwarvenkind' => 'Пояс дварфов',
            'Belt of Fire Giant Strength' => 'Пояс силы огненного великана',
            'Belt of Frost Giant Strength' => 'Пояс силы морозного великана',
            'Belt of Hill Giant Strength' => 'Пояс силы холмового великана',
            'Belt of Stone Giant Strength' => 'Пояс силы каменного великана',
            'Belt of Storm Giant Strength' => 'Пояс силы штормового великана',
            'Belt of Giant Strength' => 'Пояс силы великана',
            'Berserker Axe' => 'Секира берсерка',
            'Bowl of Commanding Water Elementals' => 'Чаша командования водяными элементалями',
            'Brass Horn of Valhalla' => 'Латунный рог Валгаллы',
            'Bronze Griffon Figurine of Wondrous Power' => 'Статуэтка чудесной силы: бронзовый грифон',
            'Bronze Horn of Valhalla' => 'Бронзовый рог Валгаллы',
            'Boots of Elvenkind' => 'Эльфийские сапоги',
            'Boots of Levitation' => 'Сапоги левитации',
            'Boots of Speed' => 'Сапоги скорости',
            'Boots of Striding and Springing' => 'Сапоги ходьбы и прыжков',
            'Boots of the Winterlands' => 'Сапоги зимних земель',
            'Bracers of Archery' => 'Наручи стрельбы из лука',
            'Bracers of Defense' => 'Наручи защиты',
            'Brazier of Commanding Fire Elementals' => 'Жаровня командования огненными элементалями',
            'Brooch of Shielding' => 'Брошь защиты',
            'Broom of Flying' => 'Помело полёта',
            'Candle of Invocation' => 'Свеча призыва',
            'Cape of the Mountebank' => 'Плащ шарлатана',
            'Carpet of Flying' => 'Ковёр-самолёт',
            'Censer of Controlling Air Elementals' => 'Кадило управления воздушными элементалями',
            'Chime of Opening' => 'Колокольчик открывания',
            'Circlet of Blasting' => 'Обруч взрыва',
            'Cloak of Arachnida' => 'Плащ паука',
            'Cloak of Displacement' => 'Плащ ускользания',
            'Cloak of Elvenkind' => 'Эльфийский плащ',
            'Cloak of Protection' => 'Плащ защиты',
            'Cloak of the Bat' => 'Плащ летучей мыши',
            'Cloak of the Manta Ray' => 'Плащ ската манта',
            'Crystal Ball' => 'Хрустальный шар',
            'Crystal Ball of Mind Reading' => 'Хрустальный шар чтения мыслей',
            'Crystal Ball of Telepathy' => 'Хрустальный шар телепатии',
            'Crystal Ball of True Seeing' => 'Хрустальный шар истинного зрения',
            'Cube of Force' => 'Куб силового поля',
            'Cubic Gate' => 'Кубические врата',
            'Dagger of Venom' => 'Кинжал яда',
            'Dancing Sword' => 'Танцующий меч',
            'Decanter of Endless Water' => 'Графин бесконечной воды',
            'Deck of Illusions' => 'Колода иллюзий',
            'Deck of Many Things' => 'Колода многих вещей',
            'Defender' => 'Защитник',
            'Demon Armor' => 'Демонический доспех',
            'Dimensional Shackles' => 'Межпространственные кандалы',
            'Dragon Scale Mail' => 'Чешуйчатый доспех дракона',
            'Dragon Slayer' => 'Драконоборец',
            'Driftglobe' => 'Парящий шар',
            'Dust of Disappearance' => 'Пыль исчезновения',
            'Dust of Dryness' => 'Пыль сухости',
            'Dust of Sneezing and Choking' => 'Пыль чихания и удушья',
            'Dwarven Plate' => 'Дварфские латы',
            'Dwarven Thrower' => 'Дварфский метатель',
            'Efficient Quiver' => 'Эффективный колчан',
            'Efreeti Bottle' => 'Бутыль ифрита',
            'Elemental Gem' => 'Самоцвет элементаля',
            'Earth Elemental Gem' => 'Самоцвет земляного элементаля',
            'Fire Elemental Gem' => 'Самоцвет огненного элементаля',
            'Water Elemental Gem' => 'Самоцвет водяного элементаля',
            'Elven Chain' => 'Эльфийская кольчуга',
            'Eversmoking Bottle' => 'Бутыль вечного дыма',
            'Eyes of Charming' => 'Очки очарования',
            'Eyes of Minute Seeing' => 'Очки детального зрения',
            'Eyes of the Eagle' => 'Очки орла',
            'Feather Token' => 'Перьевой жетон',
            'Bird Feather Token' => 'Перьевой жетон: птица',
            'Fan Feather Token' => 'Перьевой жетон: веер',
            'Swan Boat Feather Token' => 'Перьевой жетон: лебединая лодка',
            'Tree Feather Token' => 'Перьевой жетон: дерево',
            'Whip Feather Token' => 'Перьевой жетон: кнут',
            'Figurine of Wondrous Power' => 'Статуэтка чудесной силы',
            'Ebony Fly Figurine of Wondrous Power' => 'Статуэтка чудесной силы: эбеновая муха',
            'Golden Lions Figurine of Wondrous Power' => 'Статуэтка чудесной силы: золотые львы',
            'Ivory Goats Figurine of Wondrous Power' => 'Статуэтка чудесной силы: слоновые козлы',
            'Marble Elephant Figurine of Wondrous Power' => 'Статуэтка чудесной силы: мраморный слон',
            'Obsidian Steed Figurine of Wondrous Power' => 'Статуэтка чудесной силы: обсидиановый скакун',
            'Onyx Dog Figurine of Wondrous Power' => 'Статуэтка чудесной силы: ониксовая собака',
            'Serpentine Owl Figurine of Wondrous Power' => 'Статуэтка чудесной силы: змеевидная сова',
            'Silver Raven Figurine of Wondrous Power' => 'Статуэтка чудесной силы: серебряный ворон',
            'Flame Tongue' => 'Язык пламени',
            'Folding Boat' => 'Складная лодка',
            'Frost Brand' => 'Морозный клинок',
            'Gauntlets of Ogre Power' => 'Рукавицы силы огра',
            'Gem of Brightness' => 'Самоцвет яркости',
            'Gem of Seeing' => 'Самоцвет видения',
            'Giant Slayer' => 'Истребитель великанов',
            'Glamoured Studded Leather Armor' => 'Проклёпанный кожаный доспех чарующего вида',
            'Gloves of Missile Snaring' => 'Перчатки ловли снарядов',
            'Gloves of Swimming and Climbing' => 'Перчатки плавания и лазания',
            'Goggles of Night' => 'Очки ночи',
            'Hammer of Thunderbolts' => 'Молот громовых ударов',
            'Handy Haversack' => 'Удобный рюкзак',
            'Hat of Disguise' => 'Шляпа маскировки',
            'Headband of Intellect' => 'Повязка интеллекта',
            'Helm of Brilliance' => 'Шлем сияния',
            'Helm of Comprehending Languages' => 'Шлем понимания языков',
            'Helm of Telepathy' => 'Шлем телепатии',
            'Helm of Teleportation' => 'Шлем телепортации',
            'Holy Avenger' => 'Святой мститель',
            'Horn of Blasting' => 'Рог взрыва',
            'Horn of Valhalla' => 'Рог Валгаллы',
            'Iron Horn of Valhalla' => 'Железный рог Валгаллы',
            'Silver Horn of Valhalla' => 'Серебряный рог Валгаллы',
            'Horseshoes of a Zephyr' => 'Подковы зефира',
            'Horseshoes of Speed' => 'Подковы скорости',
            'Immovable Rod' => 'Неподвижный жезл',
            'Instant Fortress' => 'Мгновенная крепость',
            'Ioun Stone' => 'Камень Йоун',
            'Ioun Stone of Absorption' => 'Камень Йоун поглощения',
            'Ioun Stone of Agility' => 'Камень Йоун проворства',
            'Ioun Stone of Awareness' => 'Камень Йоун осведомлённости',
            'Ioun Stone of Fortitude' => 'Камень Йоун стойкости',
            'Ioun Stone of Greater Absorption' => 'Камень Йоун большого поглощения',
            'Ioun Stone of Insight' => 'Камень Йоун проницательности',
            'Ioun Stone of Intellect' => 'Камень Йоун интеллекта',
            'Ioun Stone of Leadership' => 'Камень Йоун лидерства',
            'Ioun Stone of Mastery' => 'Камень Йоун мастерства',
            'Ioun Stone of Protection' => 'Камень Йоун защиты',
            'Ioun Stone of Regeneration' => 'Камень Йоун регенерации',
            'Ioun Stone of Reserve' => 'Камень Йоун резерва',
            'Ioun Stone of Strength' => 'Камень Йоун силы',
            'Ioun Stone of Sustenance' => 'Камень Йоун поддержания',
            'Iron Bands of Binding' => 'Железные путы сковывания',
            'Iron Flask' => 'Железная фляга',
            'Javelin of Lightning' => 'Метательное копьё молнии',
            'Keoghtom\'s Ointment' => 'Мазь Кеогтома',
            'Lantern of Revealing' => 'Фонарь обнаружения',
            'Luck Blade' => 'Клинок удачи',
            'Mace of Disruption' => 'Булава распада',
            'Mace of Smiting' => 'Булава кары',
            'Mace of Terror' => 'Булава ужаса',
            'Mantle of Spell Resistance' => 'Мантия сопротивления заклинаниям',
            'Manual of Bodily Health' => 'Руководство телесного здоровья',
            'Manual of Gainful Exercise' => 'Руководство полезных упражнений',
            'Manual of Golems' => 'Руководство големов',
            'Manual of Clay Golems' => 'Руководство глиняных големов',
            'Manual of Flesh Golems' => 'Руководство плотяных големов',
            'Manual of Iron Golems' => 'Руководство железных големов',
            'Manual of Stone Golems' => 'Руководство каменных големов',
            'Manual of Quickness of Action' => 'Руководство быстроты действий',
            'Marvelous Pigments' => 'Чудесные краски',
            'Medallion of Thoughts' => 'Медальон мыслей',
            'Mirror of Life Trapping' => 'Зеркало заточения жизни',
            'Mithral Armor' => 'Мифриловый доспех',
            'Necklace of Adaptation' => 'Ожерелье адаптации',
            'Necklace of Fireballs' => 'Ожерелье огненных шаров',
            'Necklace of Prayer Beads' => 'Ожерелье молитвенных чёток',
            'Nine Lives Stealer' => 'Похититель девяти жизней',
            'Oathbow' => 'Клятвенный лук',
            'Oil of Etherealness' => 'Масло эфирности',
            'Oil of Sharpness' => 'Масло остроты',
            'Oil of Slipperiness' => 'Масло скольжения',
            'Orb of Dragonkind' => 'Сфера драконьего рода',
            'Pearl of Power' => 'Жемчужина силы',
            'Periapt of Health' => 'Медальон здоровья',
            'Periapt of Proof against Poison' => 'Медальон защиты от яда',
            'Periapt of Wound Closure' => 'Медальон затягивания ран',
            'Philter of Love' => 'Фильтр любви',
            'Pipes of Haunting' => 'Свирель устрашения',
            'Pipes of the Sewers' => 'Свирель канализации',
            'Plate Armor of Etherealness' => 'Латы эфирности',
            'Portable Hole' => 'Переносная дыра',
            'Potion of Animal Friendship' => 'Зелье дружбы с животными',
            'Potion of Clairvoyance' => 'Зелье ясновидения',
            'Potion of Climbing' => 'Зелье лазания',
            'Potion of Diminution' => 'Зелье уменьшения',
            'Potion of Flying' => 'Зелье полёта',
            'Potion of Gaseous Form' => 'Зелье газообразной формы',
            'Potion of Giant Strength' => 'Зелье силы великана',
            'Potion of Growth' => 'Зелье роста',
            'Potion of Healing' => 'Зелье лечения',
            'Potion of Greater Healing' => 'Зелье большего лечения',
            'Potion of Superior Healing' => 'Зелье превосходного лечения',
            'Potion of Supreme Healing' => 'Зелье высшего лечения',
            'Potion of Heroism' => 'Зелье героизма',
            'Potion of Invisibility' => 'Зелье невидимости',
            'Potion of Mind Reading' => 'Зелье чтения мыслей',
            'Potion of Poison' => 'Зелье яда',
            'Potion of Resistance' => 'Зелье сопротивления',
            'Potion of Speed' => 'Зелье скорости',
            'Potion of Water Breathing' => 'Зелье водного дыхания',
            'Restorative Ointment' => 'Восстанавливающая мазь',
            'Ring of Animal Influence' => 'Кольцо влияния на животных',
            'Ring of Djinni Summoning' => 'Кольцо призыва джинна',
            'Ring of Elemental Command' => 'Кольцо командования элементалями',
            'Ring of Air Elemental Command' => 'Кольцо командования воздушным элементалем',
            'Ring of Earth Elemental Command' => 'Кольцо командования земляным элементалем',
            'Ring of Fire Elemental Command' => 'Кольцо командования огненным элементалем',
            'Ring of Water Elemental Command' => 'Кольцо командования водяным элементалем',
            'Ring of Evasion' => 'Кольцо уклонения',
            'Ring of Feather Falling' => 'Кольцо падения пёрышком',
            'Ring of Free Action' => 'Кольцо свободных действий',
            'Ring of Invisibility' => 'Кольцо невидимости',
            'Ring of Jumping' => 'Кольцо прыжков',
            'Ring of Mind Shielding' => 'Кольцо защиты разума',
            'Ring of Protection' => 'Кольцо защиты',
            'Ring of Regeneration' => 'Кольцо регенерации',
            'Ring of Resistance' => 'Кольцо сопротивления',
            'Ring of Shooting Stars' => 'Кольцо падающих звёзд',
            'Ring of Spell Storing' => 'Кольцо хранения заклинаний',
            'Ring of Spell Turning' => 'Кольцо отражения заклинаний',
            'Ring of Swimming' => 'Кольцо плавания',
            'Ring of Telekinesis' => 'Кольцо телекинеза',
            'Ring of the Ram' => 'Кольцо тарана',
            'Ring of Three Wishes' => 'Кольцо трёх желаний',
            'Ring of Warmth' => 'Кольцо тепла',
            'Ring of Water Walking' => 'Кольцо хождения по воде',
            'Ring of X-ray Vision' => 'Кольцо проникающего зрения',
            'Robe of Eyes' => 'Мантия глаз',
            'Robe of Scintillating Colors' => 'Мантия мерцающих цветов',
            'Robe of Stars' => 'Мантия звёзд',
            'Robe of the Archmagi' => 'Мантия архимага',
            'Robe of Useful Items' => 'Мантия полезных предметов',
            'Rod of Absorption' => 'Жезл поглощения',
            'Rod of Alertness' => 'Жезл бдительности',
            'Rod of Lordly Might' => 'Жезл владычной мощи',
            'Rod of Rulership' => 'Жезл правления',
            'Rod of Security' => 'Жезл безопасности',
            'Rope of Climbing' => 'Верёвка лазания',
            'Rope of Entanglement' => 'Верёвка опутывания',
            'Scarab of Protection' => 'Скарабей защиты',
            'Scimitar of Speed' => 'Скимитар скорости',
            'Shield of Missile Attraction' => 'Щит притяжения снарядов',
            'Sending Stones' => 'Камни послания',
            'Slippers of Spider Climbing' => 'Туфли паучьего лазания',
            'Sovereign Glue' => 'Суверенный клей',
            'Spell Scroll' => 'Свиток заклинания',
            'Spell Scroll (Cantrip)' => 'Свиток заклинания (заговор)',
            'Spellguard Shield' => 'Щит защиты от заклинаний',
            'Sphere of Annihilation' => 'Сфера аннигиляции',
            'Staff of Charming' => 'Посох очарования',
            'Staff of Fire' => 'Посох огня',
            'Staff of Frost' => 'Посох холода',
            'Staff of Healing' => 'Посох лечения',
            'Staff of Power' => 'Посох могущества',
            'Staff of Striking' => 'Посох ударов',
            'Staff of Swarming Insects' => 'Посох роящихся насекомых',
            'Staff of the Magi' => 'Посох магов',
            'Staff of the Python' => 'Посох питона',
            'Staff of the Woodlands' => 'Посох леса',
            'Staff of Thunder and Lightning' => 'Посох грома и молнии',
            'Staff of Withering' => 'Посох иссушения',
            'Stone of Controlling Earth Elementals' => 'Камень контролирования земляных элементалей',
            'Stone of Good Luck (Luckstone)' => 'Камень удачи',
            'Sun Blade' => 'Солнечный клинок',
            'Sword of Life Stealing' => 'Меч кражи жизни',
            'Sword of Sharpness' => 'Меч остроты',
            'Sword of Wounding' => 'Меч ранения',
            'Talisman of Pure Good' => 'Талисман абсолютного добра',
            'Talisman of the Sphere' => 'Талисман сферы',
            'Talisman of Ultimate Evil' => 'Талисман абсолютного зла',
            'Tome of Clear Thought' => 'Том ясного мышления',
            'Tome of Leadership and Influence' => 'Том лидерства и влияния',
            'Tome of Understanding' => 'Том понимания',
            'Trident of Fish Command' => 'Трезубец командования рыбами',
            'Universal Solvent' => 'Универсальный растворитель',
            'Vicious Weapon' => 'Жестокое оружие',
            'Vorpal Sword' => 'Ворпальный меч',
            'Wand of Binding' => 'Волшебная палочка сковывания',
            'Wand of Enemy Detection' => 'Волшебная палочка обнаружения врагов',
            'Wand of Fear' => 'Волшебная палочка страха',
            'Wand of Fireballs' => 'Волшебная палочка огненных шаров',
            'Wand of Lightning Bolts' => 'Волшебная палочка молний',
            'Wand of Magic Detection' => 'Волшебная палочка обнаружения магии',
            'Wand of Magic Missiles' => 'Волшебная палочка волшебных стрел',
            'Wand of Paralysis' => 'Волшебная палочка паралича',
            'Wand of Polymorph' => 'Волшебная палочка превращения',
            'Wand of Secrets' => 'Волшебная палочка секретов',
            'Wand of the War Mage, +1, +2, or +3' => 'Волшебная палочка боевого мага, +1, +2 или +3',
            'Wand of the War Mage, +1' => 'Волшебная палочка боевого мага, +1',
            'Wand of the War Mage, +2' => 'Волшебная палочка боевого мага, +2',
            'Wand of the War Mage, +3' => 'Волшебная палочка боевого мага, +3',
            'Wand of the War Mage' => 'Волшебная палочка боевого мага',
            'Wand of Web' => 'Волшебная палочка паутины',
            'Wand of Wonder' => 'Волшебная палочка чудес',
            'Weapon, +1, +2, or +3' => 'Оружие, +1, +2 или +3',
            'Weapon, +1' => 'Оружие, +1',
            'Weapon, +2' => 'Оружие, +2',
            'Weapon, +3' => 'Оружие, +3',
            'Well of Many Worlds' => 'Колодец многих миров',
            'Wind Fan' => 'Веер ветра',
            'Winged Boots' => 'Крылатые сапоги',
            'Wings of Flying' => 'Крылья полёта',
        ];

        if (isset($direct[$name])) {
            return $direct[$name];
        }

        if (preg_match('/^Belt of (.+) Giant Strength$/', $name, $matches)) {
            return 'Пояс силы '.$this->giantKind($matches[1]).' великана';
        }

        if (preg_match('/^(.+) Dragon Scale Mail$/', $name, $matches)) {
            return 'Чешуйчатый доспех '.$this->dragonColor($matches[1]).' дракона';
        }

        if (preg_match('/^(.+) Feather Token$/', $name, $matches)) {
            return 'Перьевой жетон: '.$this->translateEnglishPhrase($matches[1]);
        }

        if (preg_match('/^Carpet of Flying \((.+)\)$/', $name, $matches)) {
            return 'Ковёр-самолёт ('.$this->translateEnglishPhrase($matches[1]).')';
        }

        if (preg_match('/^Potion of (.+) Giant Strength$/', $name, $matches)) {
            return 'Зелье силы '.$this->giantKind($matches[1]).' великана';
        }

        if (preg_match('/^(Potion|Ring) of (.+) Resistance$/', $name, $matches)) {
            return ($matches[1] === 'Potion' ? 'Зелье' : 'Кольцо').' сопротивления: '.$this->damageKind($matches[2]);
        }

        if (preg_match('/^Spell Scroll \((.+)\)$/', $name, $matches)) {
            return 'Свиток заклинания ('.$this->translateEnglishPhrase($matches[1]).')';
        }

        return $this->translateEnglishPhrase($name);
    }

    private function damageKind(string $kind): string
    {
        return [
            'Acid' => 'кислота',
            'Cold' => 'холод',
            'Fire' => 'огонь',
            'Force' => 'силовой урон',
            'Lightning' => 'электричество',
            'Necrotic' => 'некротический урон',
            'Poison' => 'яд',
            'Psychic' => 'психический урон',
            'Radiant' => 'излучение',
            'Thunder' => 'звук',
        ][$kind] ?? mb_strtolower($kind);
    }

    private function giantKind(string $kind): string
    {
        return [
            'Cloud' => 'облачного',
            'Fire' => 'огненного',
            'Frost' => 'морозного',
            'Hill' => 'холмового',
            'Stone' => 'каменного',
            'Storm' => 'штормового',
        ][$kind] ?? mb_strtolower($kind);
    }

    private function dragonColor(string $color): string
    {
        return [
            'Black' => 'чёрного',
            'Blue' => 'синего',
            'Brass' => 'латунного',
            'Bronze' => 'бронзового',
            'Copper' => 'медного',
            'Gold' => 'золотого',
            'Green' => 'зелёного',
            'Red' => 'красного',
            'Silver' => 'серебряного',
            'White' => 'белого',
        ][$color] ?? mb_strtolower($color);
    }

    private function translateEnglishPhrase(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $exact = [
            'DEX' => 'Ловкость',
            'STR' => 'Сила',
            'CON' => 'Телосложение',
            'INT' => 'Интеллект',
            'WIS' => 'Мудрость',
            'CHA' => 'Харизма',
            'passive Perception' => 'пассивная Внимательность',
            'Common' => 'Общий',
            'Abyssal' => 'Бездны',
            'Celestial' => 'Небесный',
            'Deep Speech' => 'Глубинная речь',
            'Draconic' => 'Драконий',
            'Dwarvish' => 'Дварфский',
            'Elvish' => 'Эльфийский',
            'Giant' => 'Великанский',
            'Gnomish' => 'Гномий',
            'Goblin' => 'Гоблинский',
            'Halfling' => 'Полуросликов',
            'Infernal' => 'Инфернальный',
            'Orc' => 'Орочий',
            'Primordial' => 'Первичный',
            'Sylvan' => 'Сильван',
            'Undercommon' => 'Подземный',
            'Telepathy' => 'Телепатия',
        ];

        if (isset($exact[$text])) {
            return $exact[$text];
        }

        $replacements = [
            'Wondrous item' => 'Чудесный предмет',
            'wondrous Item' => 'чудесный предмет',
            'wondrous item' => 'чудесный предмет',
            'Weapon (any ammunition)' => 'Оружие (любой боеприпас)',
            'Weapon (arrow)' => 'Оружие (стрела)',
            'Weapon (any sword)' => 'Оружие (любой меч)',
            'Weapon (any)' => 'Оружие (любое)',
            'requires attunement' => 'требуется настройка',
            'Requires Attunement' => 'требуется настройка',
            'requires attunement by a spellcaster' => 'требуется настройка заклинателем',
            'Armor' => 'Доспех',
            'armor' => 'доспех',
            'Weapon' => 'Оружие',
            'weapon' => 'оружие',
            'Potion' => 'Зелье',
            'potion' => 'зелье',
            'Ring' => 'Кольцо',
            'Rod' => 'Жезл',
            'Staff' => 'Посох',
            'Wand' => 'Волшебная палочка',
            'Scroll' => 'Свиток',
            'uncommon' => 'необычный',
            'common' => 'обычный',
            'rare' => 'редкий',
            'very rare' => 'очень редкий',
            'legendary' => 'легендарный',
            'artifact' => 'артефакт',
            'Varies' => 'разная',
            'Cantrip' => 'заговор',
            '1st' => '1-й уровень',
            '2nd' => '2-й уровень',
            '3rd' => '3-й уровень',
            '4th' => '4-й уровень',
            '5th' => '5-й уровень',
            '6th' => '6-й уровень',
            '7th' => '7-й уровень',
            '8th' => '8-й уровень',
            '9th' => '9-й уровень',
            'Armor (medium or heavy, but not hide)' => 'Доспех (средний или тяжёлый, но не шкурный)',
            'Weapon (any melee weapon)' => 'Оружие (любое рукопашное)',
            'Weapon (any martial weapon)' => 'Оружие (любое воинское)',
            'Weapon (any weapon that deals slashing damage)' => 'Оружие (любое оружие, наносящее рубящий урон)',
            'Weapon (any sword that deals slashing damage)' => 'Оружие (любой меч, наносящий рубящий урон)',
            'Weapon (javelin)' => 'Оружие (метательное копьё)',
            'Weapon (longsword)' => 'Оружие (длинный меч)',
            'Weapon (mace)' => 'Оружие (булава)',
            'Weapon (maul)' => 'Оружие (молот)',
            'Weapon (scimitar)' => 'Оружие (скимитар)',
            'Weapon (sword)' => 'Оружие (меч)',
            'Weapon (trident)' => 'Оружие (трезубец)',
            'You can use an action' => 'Вы можете действием',
            'You can use your action' => 'Вы можете действием',
            'You can speak' => 'Вы можете произнести',
            'you can use an action to' => 'вы можете действием',
            'You can use an action to' => 'Вы можете действием',
            'you can use an action' => 'вы можете действием',
            'You can use an action' => 'Вы можете действием',
            'to speak the command word' => 'произнести командное слово',
            'to blow this horn' => 'протрубить в этот рог',
            'to throw' => 'бросить',
            'to toss' => 'подбросить',
            'This bowl is filled with water' => 'Эта чаша наполнена водой',
            'While this bowl is filled with water' => 'Пока эта чаша наполнена водой',
            'The bowl can\'t be used this way again until the next dawn.' => 'Чашу нельзя использовать таким образом снова до следующего рассвета.',
            'the next dawn' => 'следующего рассвета',
            'command word' => 'командное слово',
            'summon' => 'призвать',
            'water elemental' => 'водяного элементаля',
            'warrior spirits' => 'духи воинов',
            'appear within' => 'появляются в пределах',
            'They use the statistics of a berserker.' => 'Они используют характеристики берсерка.',
            'They return to Valhalla after 1 hour or when they drop to 0 hit points.' => 'Они возвращаются в Валгаллу через 1 час или когда их хиты опускаются до 0.',
            'A figurine of wondrous power is a statuette of a beast small enough to fit in a pocket.' => 'Статуэтка чудесной силы — это фигурка зверя, достаточно маленькая, чтобы поместиться в карман.',
            'This item' => 'Этот предмет',
            'This weapon' => 'Это оружие',
            'This armor' => 'Этот доспех',
            'This ring' => 'Это кольцо',
            'This cape' => 'Этот плащ',
            'This candle' => 'Эта свеча',
            'This carpet' => 'Этот ковёр',
            'This shield' => 'Этот щит',
            'This potion' => 'Это зелье',
            'This spell scroll' => 'Этот свиток заклинания',
            'This suit of armor is reinforced with adamantine, one of the hardest substances in existence.' => 'Этот комплект доспеха усилен адамантином, одним из самых твёрдых веществ.',
            'While you\'re wearing it, any critical hit against you becomes a normal hit.' => 'Пока вы носите его, любой критический удар по вам становится обычным попаданием.',
            'You have a bonus to attack and damage rolls made with this piece of magic ammunition.' => 'Вы получаете бонус к броскам атаки и урона этим магическим боеприпасом.',
            'The bonus is determined by the rarity of the ammunition.' => 'Бонус зависит от редкости боеприпаса.',
            'Once it hits a target, the ammunition is no longer magical.' => 'После попадания в цель боеприпас перестаёт быть магическим.',
            'You have a +1 bonus to attack and damage rolls made with this piece of magic ammunition.' => 'Вы получаете бонус +1 к броскам атаки и урона этим магическим боеприпасом.',
            'You have a +2 bonus to attack and damage rolls made with this piece of magic ammunition.' => 'Вы получаете бонус +2 к броскам атаки и урона этим магическим боеприпасом.',
            'You have a +3 bonus to attack and damage rolls made with this piece of magic ammunition.' => 'Вы получаете бонус +3 к броскам атаки и урона этим магическим боеприпасом.',
            'Your Constitution score is 19 while you wear this amulet.' => 'Пока вы носите этот амулет, ваше значение Телосложения равно 19.',
            'It has no effect on you if your Constitution is already 19 or higher' => 'Он не даёт эффекта, если ваше Телосложение уже 19 или выше',
            'While wearing this amulet, you are hidden from divination magic.' => 'Пока вы носите этот амулет, вы скрыты от магии прорицания.',
            'You can\'t be targeted by such magic or perceived through magical scrying sensors.' => 'Такая магия не может выбрать вас целью или обнаружить через магические сенсоры наблюдения.',
            'This cape smells faintly of brimstone.' => 'Этот плащ едва пахнет серой.',
            'While you wear it, you can use it to cast the dimension door spell as an action.' => 'Пока вы носите его, вы можете действием сотворить через него заклинание дверь в пространстве.',
            'This property of the cape can\'t be used again until the next dawn.' => 'Это свойство плаща нельзя использовать снова до следующего рассвета.',
            'You can speak the carpet\'s command word as an action to make the carpet hover and fly.' => 'Вы можете действием произнести командное слово ковра, чтобы он завис и полетел.',
            'It moves according to your spoken directions' => 'Он движется согласно вашим устным указаниям',
            'provided that you are within' => 'если вы находитесь в пределах',
            'of it' => 'от него',
            'A blink dog takes its name from its ability to blink in and out of existence, a talent it uses to aid its attacks and to avoid harm.' => 'Мерцающая собака получила своё название за способность исчезать и появляться снова; она использует это, чтобы атаковать и избегать вреда.',
            'Taking its name from its crimson feathers and aggressive nature' => 'Кровавый ястреб получил название за багровые перья и агрессивный нрав',
            'This tiny object looks like' => 'Этот крошечный предмет выглядит как',
            'An awakened tree is an ordinary tree given sentience and mobility by the awaken spell or similar magic.' => 'Пробуждённое дерево — обычное дерево, которому заклинание пробуждение или подобная магия даровали разум и способность двигаться.',
            'An awakened shrub is an ordinary shrub given sentience and mobility by the awaken spell or similar magic.' => 'Пробуждённый куст — обычный куст, которому заклинание пробуждение или подобная магия даровали разум и способность двигаться.',
            'An axe beak is a tall flightless bird with strong legs and a heavy, wedge-shaped beak. It has a nasty disposition and tends to attack any unfamiliar creature that wanders too close.' => 'Топороклюв — высокая нелетающая птица с сильными ногами и тяжёлым клиновидным клювом. Он злобен и часто нападает на незнакомых существ, подошедших слишком близко.',
            'This suit of armor' => 'Этот комплект доспеха',
            'While wearing' => 'Пока вы носите',
            'While holding' => 'Пока вы держите',
            'you can use an action' => 'вы можете действием',
            'you gain' => 'вы получаете',
            'You gain' => 'Вы получаете',
            'your Strength score changes to' => 'ваше значение Силы становится',
            'Your Strength score changes to' => 'Ваше значение Силы становится',
            'your Constitution score increases by 2' => 'ваше значение Телосложения увеличивается на 2',
            'Your Constitution score increases by 2' => 'Ваше значение Телосложения увеличивается на 2',
            'you have' => 'у вас есть',
            'You have' => 'У вас есть',
            'while wearing' => 'пока вы носите',
            'while holding' => 'пока вы держите',
            'while you wear' => 'пока вы носите',
            'while you hold' => 'пока вы держите',
            'This belt' => 'Этот пояс',
            'this belt' => 'этот пояс',
            'this item' => 'этот предмет',
            'this armor' => 'этот доспех',
            'this weapon' => 'это оружие',
            'this ring' => 'это кольцо',
            'charges' => 'заряды',
            'charge' => 'заряд',
            'regains' => 'восстанавливает',
            'at dawn' => 'на рассвете',
            'saving throw' => 'спасбросок',
            'saving throws' => 'спасброски',
            'spell save DC' => 'Сл спасброска заклинания',
            'attack roll' => 'бросок атаки',
            'attack rolls' => 'броски атаки',
            'ability check' => 'проверка характеристики',
            'ability checks' => 'проверки характеристик',
            'checks' => 'проверки',
            'advantage' => 'преимущество',
            'disadvantage' => 'помеха',
            'hit points' => 'хиты',
            'damage' => 'урон',
            'acid' => 'кислота',
            'bludgeoning' => 'дробящий',
            'cold' => 'холод',
            'fire' => 'огонь',
            'force' => 'силовой',
            'lightning' => 'электричество',
            'necrotic' => 'некротический',
            'piercing' => 'колющий',
            'poison' => 'яд',
            'psychic' => 'психический',
            'radiant' => 'излучение',
            'slashing' => 'рубящий',
            'thunder' => 'звук',
            'magical' => 'магический',
            'nonmagical' => 'немагический',
            'melee weapon attack' => 'рукопашная атака оружием',
            'ranged weapon attack' => 'дальнобойная атака оружием',
            'melee spell attack' => 'рукопашная атака заклинанием',
            'ranged spell attack' => 'дальнобойная атака заклинанием',
            'Hit:' => 'Попадание:',
            'Miss:' => 'Промах:',
            'Attack:' => 'Атака:',
            'Recharge' => 'Перезарядка',
            'Legendary Resistance' => 'Легендарное сопротивление',
            'Magic Resistance' => 'Сопротивление магии',
            'Magic Weapons' => 'Магическое оружие',
            'Multiattack' => 'Мультиатака',
            'Dagger' => 'Кинжал',
            'Rake' => 'Царапанье',
            'Beak' => 'Клюв',
            'Warhammer' => 'Боевой молот',
            'Bite' => 'Укус',
            'Claw' => 'Коготь',
            'Tail' => 'Хвост',
            'Slam' => 'Удар',
            'Frightful Presence' => 'Пугающее присутствие',
            'Breath Weapon' => 'Оружие дыхания',
            'Detect' => 'Обнаружение',
            'Tail Swipe' => 'Удар хвостом',
            'Psychic Drain' => 'Психическое вытягивание',
            'Actions' => 'Действия',
            'Legendary Actions' => 'Легендарные действия',
            'one target' => 'одна цель',
            'one creature' => 'одно существо',
            'the target' => 'цель',
            'The armor makes two melee attacks.' => 'Доспех совершает две рукопашные атаки.',
            'The ettin makes two attacks: one with its battleaxe and one with its morningstar.' => 'Эттин совершает две атаки: одну секирой и одну моргенштерном.',
            'The giant makes two greataxe attacks.' => 'Великан совершает две атаки секирой.',
            'The sphinx makes two claw attacks.' => 'Сфинкс совершает две атаки когтями.',
            'The aboleth makes three tentacle attacks.' => 'Аболет совершает три атаки щупальцами.',
            'The balor makes two attacks: one with its longsword and one with its whip.' => 'Балор совершает две атаки: одну длинным мечом и одну кнутом.',
            'The captain makes three melee attacks: two with its scimitar and one with its dagger.' => 'Капитан совершает три рукопашные атаки: две скимитаром и одну кинжалом.',
            'Or the captain makes two ranged attacks with its daggers.' => 'Или капитан совершает две дальнобойные атаки кинжалами.',
            'The devil makes three melee attacks: one with its tail and two with its claws.' => 'Дьявол совершает три рукопашные атаки: одну хвостом и две когтями.',
            'The devil makes two attacks: one with its beard and one with its glaive.' => 'Дьявол совершает две атаки: одну бородой и одну глефой.',
            'Alternatively, it can use Hurl Flame twice.' => 'Вместо этого он может дважды использовать Метание пламени.',
            'The assassin makes two shortsword attacks.' => 'Убийца совершает две атаки коротким мечом.',
            'The aboleth makes a Wisdom (Perception) check.' => 'Аболет совершает проверку Мудрости (Внимательность).',
            'The aboleth makes one tail attack.' => 'Аболет совершает одну атаку хвостом.',
            'The aboleth can breathe air and water.' => 'Аболет может дышать воздухом и водой.',
            'The octopus can breathe only underwater.' => 'Осьминог может дышать только под водой.',
            'While out of water, the octopus can hold its breath for 30 minutes.' => 'Вне воды осьминог может задерживать дыхание на 30 минут.',
            'Magical darkness doesn\'t impede the imp\'s darkvision.' => 'Магическая тьма не мешает тёмному зрению беса.',
            'The badger has advantage on Wisdom (Perception) checks that rely on smell.' => 'Барсук получает преимущество на проверки Мудрости (Внимательность), полагающиеся на обоняние.',
            'The bear has advantage on Wisdom (Perception) checks that rely on smell.' => 'Медведь получает преимущество на проверки Мудрости (Внимательность), полагающиеся на обоняние.',
            'Melee Weapon Attack' => 'Рукопашная атака оружием',
            'Ranged Weapon Attack' => 'Дальнобойная атака оружием',
            'Melee or Ranged Weapon Attack' => 'Рукопашная или дальнобойная атака оружием',
            'to hit' => 'к попаданию',
            'one target' => 'одна цель',
            'Hit:' => 'Попадание:',
            'piercing damage' => 'колющего урона',
            'slashing damage' => 'рубящего урона',
            'bludgeoning damage' => 'дробящего урона',
            'acid damage' => 'урона кислотой',
            'poison damage' => 'урона ядом',
            'psychic damage' => 'психического урона',
            'fire damage' => 'урона огнём',
            'cold damage' => 'урона холодом',
            'lightning damage' => 'урона электричеством',
            'thunder damage' => 'урона звуком',
            'necrotic damage' => 'некротического урона',
            'radiant damage' => 'урона излучением',
            'force damage' => 'силового урона',
            'The target' => 'Цель',
            'The creature' => 'Существо',
            'The armor' => 'Доспех',
            'The assassin' => 'Убийца',
            'The aboleth' => 'Аболет',
            'The ettin' => 'Эттин',
            'The giant' => 'Великан',
            'The octopus' => 'Осьминог',
            'The weasel' => 'Ласка',
            'The hound' => 'Гончая',
            'The azer' => 'Азер',
            'The sphinx' => 'Сфинкс',
            'The archmage' => 'Архимаг',
            'The balor' => 'Балор',
            'The imp' => 'Бес',
            'the aboleth' => 'аболет',
            'the ettin' => 'эттин',
            'the giant' => 'великан',
            'the octopus' => 'осьминог',
            'the weasel' => 'ласка',
            'the hound' => 'гончая',
            'the azer' => 'азер',
            'the sphinx' => 'сфинкс',
            'the archmage' => 'архимаг',
            'the balor' => 'балор',
            'the imp' => 'бес',
            'makes two' => 'совершает две',
            'makes one' => 'совершает одну',
            'makes a' => 'совершает',
            'makes three' => 'совершает три',
            'shortsword attacks' => 'атаки коротким мечом',
            'ranged attacks' => 'дальнобойные атаки',
            'longsword' => 'длинный меч',
            'battleaxe' => 'секира',
            'morningstar' => 'моргенштерн',
            'whip' => 'кнут',
            'scimitar' => 'скимитар',
            'dagger' => 'кинжал',
            'tail' => 'хвост',
            'claws' => 'когти',
            'beard' => 'борода',
            'glaive' => 'глефа',
            'Greataxe' => 'Секира',
            'greataxe' => 'секира',
            'Hurl Flame' => 'Метание пламени',
            'melee attacks' => 'рукопашные атаки',
            'tail attack' => 'атаку хвостом',
            'tentacle attacks' => 'атаки щупальцами',
            'two attacks' => 'две атаки',
            'three attacks' => 'три атаки',
            'one with its bite and one with its claws' => 'одну укусом и одну когтями',
            'one with its claws and one with its sting' => 'одну когтями и одну жалом',
            'two with its claws and one with its sting' => 'две когтями и одну жалом',
            'one with its bite' => 'одну укусом',
            'one with its claws' => 'одну когтями',
            'check' => 'проверку',
            'creature' => 'существо',
            'target' => 'цель',
            'feet' => 'футов',
            'ft.' => 'фт.',
            'reach' => 'досягаемость',
            'range' => 'дистанция',
            'must succeed on' => 'должна преуспеть в',
            'must make' => 'должна совершить',
            'must make on' => 'должна совершить',
            'on a failed save' => 'при провале спасброска',
            'on a successful save' => 'при успешном спасброске',
            'against being' => 'против состояний',
            'is frightened' => 'становится испуганной',
            'is poisoned' => 'становится отравленной',
            'is paralyzed' => 'становится парализованной',
            'is restrained' => 'становится опутанной',
            'is knocked prone' => 'сбивается с ног',
            'is grappled' => 'становится схваченной',
            'escape DC' => 'Сл высвобождения',
            'plus' => 'плюс',
            ' or ' => ' или ',
            ' and ' => ' и ',
            'If ' => 'Если ',
            ' if ' => ' если ',
            'until ' => 'до тех пор, пока ',
            'Until ' => 'До тех пор, пока ',
            'until the end of its next turn' => 'до конца своего следующего хода',
            'for 1 minute' => 'на 1 минуту',
            'The monster' => 'Существо',
            'The dragon' => 'Дракон',
            'can take' => 'может совершить',
            'as a bonus action' => 'бонусным действием',
            'as a reaction' => 'реакцией',
            'spell' => 'заклинание',
            'spells' => 'заклинания',
            'Strength' => 'Сила',
            'Dexterity' => 'Ловкость',
            'Constitution' => 'Телосложение',
            'Intelligence' => 'Интеллект',
            'Wisdom' => 'Мудрость',
            'Charisma' => 'Харизма',
            'Perception' => 'Внимательность',
            'Animal Handling' => 'Уход за животными',
            'Stealth' => 'Скрытность',
            'History' => 'История',
            'Arcana' => 'Магия',
            'Athletics' => 'Атлетика',
            'Acrobatics' => 'Акробатика',
            'Insight' => 'Проницательность',
            'Investigation' => 'Анализ',
            'Persuasion' => 'Убеждение',
            'Deception' => 'Обман',
            'Intimidation' => 'Запугивание',
            'Medicine' => 'Медицина',
            'Nature' => 'Природа',
            'Religion' => 'Религия',
            'Survival' => 'Выживание',
            'Amphibious' => 'Амфибия',
            'Mucous Cloud' => 'Слизистое облако',
            'Probing Telepathy' => 'Проникающая телепатия',
            'Enslave' => 'Порабощение',
            'Two Heads' => 'Две головы',
            'Wakeful' => 'Бодрствование',
            'Hold Breath' => 'Задержка дыхания',
            'Underwater Camouflage' => 'Подводная маскировка',
            'Water Breathing' => 'Водное дыхание',
            'Ink Cloud' => 'Чернильное облако',
            'Keen Hearing and Smell' => 'Острый слух и нюх',
            'Keen Smell' => 'Острый нюх',
            'Pack Tactics' => 'Тактика стаи',
            'Fire Breath' => 'Огненное дыхание',
            'Heated Body' => 'Раскалённое тело',
            'Heated Weapons' => 'Раскалённое оружие',
            'Illumination' => 'Свечение',
            'Inscrutable' => 'Непостижимый',
            'Spellcasting' => 'Использование заклинаний',
            'Roar' => 'Рёв',
            'Claw Attack' => 'Атака когтем',
            'Teleport' => 'Телепортация',
            'Cast a Spell' => 'Сотворить заклинание',
            'Acid Spray' => 'Кислотная струя',
            'Brute' => 'Грубиян',
            'Surprise Attack' => 'Внезапная атака',
            'Death Throes' => 'Предсмертный взрыв',
            'Fire Aura' => 'Огненная аура',
            'Invisibility' => 'Невидимость',
            'Shapechanger' => 'Перевёртыш',
            'Devil\'s Sight' => 'Дьявольское зрение',
            'Tentacle' => 'Щупальце',
            'Tentacles' => 'Щупальца',
            'Battleaxe' => 'Секира',
            'Morningstar' => 'Моргенштерн',
            'Javelin' => 'Метательное копьё',
            'Light Crossbow' => 'Лёгкий арбалет',
            'Sting' => 'Жало',
            'Pincer' => 'Клешня',
            'Constrict' => 'Сжимание',
            'Gore' => 'Таран рогами',
            'Hooves' => 'Копыта',
            'Ram' => 'Таран',
            'Rock' => 'Камень',
            'Spear' => 'Копьё',
            'Longbow' => 'Длинный лук',
            'Shortbow' => 'Короткий лук',
            'Shortsword' => 'Короткий меч',
            'Greatclub' => 'Большая дубина',
        ];

        uksort($replacements, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }



    private function equipmentNameById(string $id): string
    {
        static $map = null;

        if ($map === null) {
            $map = [];

            foreach (['tools.json', 'weapons.json', 'armor.json', 'adventuring_gear.json'] as $file) {
                $items = $this->readJson($file, []);

                if (! is_array($items)) {
                    continue;
                }

                foreach ($items as $item) {
                    if (is_array($item) && isset($item['id'], $item['name_ru'])) {
                        $map[(string) $item['id']] = (string) $item['name_ru'];
                    }
                }
            }
        }

        return $map[$id] ?? '';
    }

    private function translatedEntryName(string $name): string
    {
        return [
            'Currency Units' => 'Валюты',
            'Weapon Properties' => 'Свойства оружия',
            'Rarities' => 'Редкости',
            'Notes' => 'Заметки',
            'Individual' => 'Индивидуальная добыча',
            'Hoard' => 'Клады',
            'All armor' => 'Все доспехи',
            'Light Armor' => 'Лёгкие доспехи',
            'Medium Armor' => 'Средние доспехи',
            'Heavy Armor' => 'Тяжёлые доспехи',
            'Shields' => 'Щиты',
            'Simple Weapons' => 'Простое оружие',
            'Martial Weapons' => 'Воинское оружие',
            'Crossbows, heavy' => 'Тяжёлые арбалеты',
            'Crossbows, light' => 'Лёгкие арбалеты',
            'Hand crossbows' => 'Ручные арбалеты',
            'Land Vehicles' => 'Наземный транспорт',
            'Water Vehicles' => 'Водный транспорт',
            'Padded Armor' => 'Стёганый доспех',
            'Leather Armor' => 'Кожаный доспех',
            'Studded Leather Armor' => 'Проклёпанный кожаный доспех',
            'Hide Armor' => 'Шкурный доспех',
            'Half Plate Armor' => 'Полулаты',
            'Splint Armor' => 'Шинный доспех',
            'Plate Armor' => 'Латы',
            'Saving Throw: STR' => 'Спасбросок: Сила',
            'Saving Throw: DEX' => 'Спасбросок: Ловкость',
            'Saving Throw: CON' => 'Спасбросок: Телосложение',
            'Saving Throw: INT' => 'Спасбросок: Интеллект',
            'Saving Throw: WIS' => 'Спасбросок: Мудрость',
            'Saving Throw: CHA' => 'Спасбросок: Харизма',
            'Skill: Acrobatics' => 'Навык: Акробатика',
            'Skill: Animal Handling' => 'Навык: Уход за животными',
            'Skill: Arcana' => 'Навык: Магия',
            'Skill: Athletics' => 'Навык: Атлетика',
            'Skill: Deception' => 'Навык: Обман',
            'Skill: History' => 'Навык: История',
            'Skill: Insight' => 'Навык: Проницательность',
            'Skill: Intimidation' => 'Навык: Запугивание',
            'Skill: Investigation' => 'Навык: Анализ',
            'Skill: Medicine' => 'Навык: Медицина',
            'Skill: Nature' => 'Навык: Природа',
            'Skill: Perception' => 'Навык: Внимательность',
            'Skill: Performance' => 'Навык: Выступление',
            'Skill: Persuasion' => 'Навык: Убеждение',
            'Skill: Religion' => 'Навык: Религия',
            'Skill: Sleight of Hand' => 'Навык: Ловкость рук',
            'Skill: Stealth' => 'Навык: Скрытность',
            'Skill: Survival' => 'Навык: Выживание',
            'Barbarian' => 'Варвар',
            'Bard' => 'Бард',
            'Cleric' => 'Жрец',
            'Druid' => 'Друид',
            'Fighter' => 'Воин',
            'Monk' => 'Монах',
            'Paladin' => 'Паладин',
            'Ranger' => 'Следопыт',
            'Rogue' => 'Плут',
            'Sorcerer' => 'Чародей',
            'Warlock' => 'Колдун',
            'Wizard' => 'Волшебник',
            'Dwarf' => 'Дварф',
            'Elf' => 'Эльф',
            'Halfling' => 'Полурослик',
            'Human' => 'Человек',
            'Dragonborn' => 'Драконорождённый',
            'Gnome' => 'Гном',
            'Half-Elf' => 'Полуэльф',
            'Half-Orc' => 'Полуорк',
            'Tiefling' => 'Тифлинг',
        ][$name] ?? $name;
    }

    private function translateTerm(mixed $value): string
    {
        $text = $this->flattenText($value);

        if ($text === '') {
            return '';
        }

        $direct = [
            'name_ru' => 'Название',
            'value' => 'Значение',
            'Artisan\'s Tools' => 'ремесленные инструменты',
            'Gaming Sets' => 'игровые наборы',
            'Musical Instruments' => 'музыкальные инструменты',
            'Other Tools' => 'прочие инструменты',
            'Armor' => 'доспехи',
            'Ammunition' => 'боеприпасы',
            'Varies' => 'разная',
            'wondrous_item' => 'чудесный предмет',
            'scroll' => 'свиток',
            'book' => 'книга',
            'universal_spell_scroll' => 'универсальный свиток',
            'study' => 'изучение',
            'action' => 'действие',
            'bonus_action' => 'бонусное действие',
            'reaction' => 'реакция',
            'verbal' => 'вербальный',
            'somatic' => 'соматический',
            'material' => 'материальный',
            'line' => 'линия',
            'cube' => 'куб',
            'sphere' => 'сфера',
            'single_target' => 'одна цель',
            'surface' => 'поверхность',
            'self' => 'на себя',
            'targets' => 'несколько целей',
            'bridge' => 'мост',
            'healing' => 'лечение',
            'condition' => 'особое условие',
            'spell' => 'заклинание',
            'armor' => 'доспех',
            'Wondrous Items' => 'чудесный предмет',
            'Wondrous Item' => 'чудесный предмет',
            'Ring' => 'кольцо',
            'Rod' => 'жезл',
            'Staff' => 'посох',
            'Wand' => 'волшебная палочка',
            'Scroll' => 'свиток',
            'Potion' => 'зелье',
            'Weapons' => 'оружие',
            'Simple Weapons' => 'простое оружие',
            'Martial Weapons' => 'воинское оружие',
            'Common' => 'обычный',
            'Uncommon' => 'необычный',
            'Rare' => 'редкий',
            'Very Rare' => 'очень редкий',
            'Legendary' => 'легендарный',
            'Artifact' => 'артефакт',
            'natural' => 'природный',
            'Natural' => 'природный',
            'dex' => 'Ловкость',
            'str' => 'Сила',
            'con' => 'Телосложение',
            'int' => 'Интеллект',
            'wis' => 'Мудрость',
            'cha' => 'Харизма',
            'acid' => 'кислота',
            'bludgeoning' => 'дробящий',
            'cold' => 'холод',
            'fire' => 'огонь',
            'force' => 'силовой',
            'lightning' => 'электричество',
            'necrotic' => 'некротический',
            'piercing' => 'колющий',
            'poison' => 'яд',
            'psychic' => 'психический',
            'radiant' => 'излучение',
            'slashing' => 'рубящий',
            'thunder' => 'звук',
            'bludgeoning, piercing, and slashing from nonmagical weapons' => 'дробящий, колющий и рубящий от немагического оружия',
            'Exhaustion' => 'Истощение',
            'Grappled' => 'Схваченный',
            'Paralyzed' => 'Парализованный',
            'Petrified' => 'Окаменевший',
            'Poisoned' => 'Отравленный',
            'Prone' => 'Лежащий ничком',
            'Restrained' => 'Опутанный',
            'Unconscious' => 'Бессознательный',
            'Blinded' => 'Ослеплённый',
            'Charmed' => 'Очарованный',
            'Deafened' => 'Оглохший',
            'Frightened' => 'Испуганный',
            'Incapacitated' => 'Недееспособный',
            'Invisible' => 'Невидимый',
            'Stunned' => 'Ошеломлённый',
            'major' => 'долговременное',
            'minor' => 'кратковременное',
            'chronic' => 'хроническое',
            'ending_duration' => 'окончание длительности',
            'calm_emotions' => 'Успокоение эмоций',
            'greater_restoration' => 'Высшее восстановление',
            'tiny' => 'крошечный',
            'small' => 'маленький',
            'medium' => 'средний',
            'large' => 'большой',
            'huge' => 'огромный',
            'gargantuan' => 'громадный',
            'Tiny' => 'крошечный',
            'Small' => 'маленький',
            'Medium' => 'средний',
            'Large' => 'большой',
            'Huge' => 'огромный',
            'Gargantuan' => 'громадный',
            'aberration' => 'аберрация',
            'beast' => 'зверь',
            'celestial' => 'небожитель',
            'construct' => 'конструкт',
            'dragon' => 'дракон',
            'elemental' => 'элементаль',
            'fey' => 'фея',
            'fiend' => 'исчадие',
            'giant' => 'великан',
            'humanoid' => 'гуманоид',
            'monstrosity' => 'чудовище',
            'ooze' => 'слизь',
            'plant' => 'растение',
            'undead' => 'нежить',
            'unaligned' => 'без мировоззрения',
            'lawful good' => 'законопослушный добрый',
            'neutral good' => 'нейтральный добрый',
            'chaotic good' => 'хаотичный добрый',
            'lawful neutral' => 'законопослушный нейтральный',
            'neutral' => 'нейтральный',
            'chaotic neutral' => 'хаотичный нейтральный',
            'lawful evil' => 'законопослушный злой',
            'neutral evil' => 'нейтральный злой',
            'chaotic evil' => 'хаотичный злой',
        ];

        if (isset($direct[$text])) {
            return $direct[$text];
        }

        $text = str_replace([
            ' ft.',
            'feet',
            'Name Ru:',
            'Gp Value:',
            'Value Gp:',
            'Works For:',
            'Severity:',
        ], [
            ' фт.',
            'футов',
            'Название:',
            'Стоимость в золотых:',
            'Цена:',
            'Подходит для:',
            'Тяжесть:',
        ], $text);

        return $text;
    }

    private function monsterName(string $name): string
    {
        $direct = [
            'Aboleth' => 'Аболет',
            'Acolyte' => 'Послушник',
            'Bandit' => 'Бандит',
            'Commoner' => 'Простолюдин',
            'Cultist' => 'Культист',
            'Goblin' => 'Гоблин',
            'Hobgoblin' => 'Хобгоблин',
            'Kobold' => 'Кобольд',
            'Orc' => 'Орк',
            'Skeleton' => 'Скелет',
            'Zombie' => 'Зомби',
            'Androsphinx' => 'Андросфинкс',
            'Animated Armor' => 'Оживлённый доспех',
            'Ankheg' => 'Анхег',
            'Archmage' => 'Архимаг',
            'Assassin' => 'Убийца',
            'Awakened Shrub' => 'Пробуждённый куст',
            'Awakened Tree' => 'Пробуждённое дерево',
            'Axe Beak' => 'Топороклюв',
            'Azer' => 'Азер',
            'Baboon' => 'Павиан',
            'Badger' => 'Барсук',
            'Gynosphinx' => 'Гиносфинкс',
            'Balor' => 'Балор',
            'Bandit Captain' => 'Капитан бандитов',
            'Barbed Devil' => 'Шипастый дьявол',
            'Bearded Devil' => 'Бородатый дьявол',
            'Berserker' => 'Берсерк',
            'Ettin' => 'Эттин',
            'Frost Giant' => 'Морозный великан',
            'Octopus' => 'Осьминог',
            'Weasel' => 'Ласка',
            'Black Bear' => 'Чёрный медведь',
            'Black Pudding' => 'Чёрная жижа',
            'Blink Dog' => 'Мерцающая собака',
            'Blood Hawk' => 'Кровавый ястреб',
            'Bone Devil' => 'Костяной дьявол',
            'Brown Bear' => 'Бурый медведь',
            'Bugbear' => 'Багбир',
            'Chain Devil' => 'Цепной дьявол',
            'Chuul' => 'Чуул',
            'Clay Golem' => 'Глиняный голем',
            'Cloaker' => 'Плащевик',
            'Cloud Giant' => 'Облачный великан',
            'Cockatrice' => 'Кокатрикс',
            'Constrictor Snake' => 'Удав',
            'Couatl' => 'Коатль',
            'Crab' => 'Краб',
            'Cult Fanatic' => 'Фанатик культа',
            'Darkmantle' => 'Тёмная мантия',
            'Death Dog' => 'Смертельная собака',
            'Deep Gnome (Svirfneblin)' => 'Глубинный гном (свирфнеблин)',
            'Deer' => 'Олень',
            'Deva' => 'Дэва',
            'Djinni' => 'Джинн',
            'Draft Horse' => 'Тягловая лошадь',
            'Dragon Turtle' => 'Драконья черепаха',
            'Dretch' => 'Дретч',
            'Drider' => 'Драйдер',
            'Drow' => 'Дроу',
            'Druid' => 'Друид',
            'Duergar' => 'Дуэргар',
            'Dust Mephit' => 'Пылевой мефит',
            'Efreeti' => 'Ифрит',
            'Erinyes' => 'Эриния',
            'Ettercap' => 'Эттеркап',
            'Flesh Golem' => 'Плотяной голем',
            'Flying Snake' => 'Летающая змея',
            'Flying Sword' => 'Летающий меч',
            'Gelatinous Cube' => 'Студенистый куб',
            'Ghast' => 'Упырь',
            'Ghost' => 'Привидение',
            'Giant Centipede' => 'Гигантская многоножка',
            'Giant Fire Beetle' => 'Гигантский огненный жук',
            'Giant Octopus' => 'Гигантский осьминог',
            'Giant Poisonous Snake' => 'Гигантская ядовитая змея',
            'Giant Rat (Diseased)' => 'Гигантская крыса (больная)',
            'Giant Sea Horse' => 'Гигантский морской конёк',
            'Giant Toad' => 'Гигантская жаба',
            'Giant Weasel' => 'Гигантская ласка',
            'Giant Wolf Spider' => 'Гигантский волчий паук',
            'Gibbering Mouther' => 'Бормочущая пасть',
            'Glabrezu' => 'Глабрезу',
            'Gladiator' => 'Гладиатор',
            'Gnoll' => 'Гнолл',
            'Gorgon' => 'Горгона',
            'Gray Ooze' => 'Серая слизь',
            'Green Hag' => 'Зелёная карга',
            'Grick' => 'Грик',
            'Grimlock' => 'Гримлок',
            'Guard' => 'Стражник',
            'Guardian Naga' => 'Нага-хранитель',
            'Half-Red Dragon Veteran' => 'Ветеран-полукрасный дракон',
            'Hawk' => 'Ястреб',
            'Hell Hound' => 'Адская гончая',
            'Hezrou' => 'Хезроу',
            'Hill Giant' => 'Холмовой великан',
            'Hippogriff' => 'Гиппогриф',
            'Homunculus' => 'Гомункул',
            'Horned Devil' => 'Рогатый дьявол',
            'Hunter Shark' => 'Охотничья акула',
            'Ice Devil' => 'Ледяной дьявол',
            'Ice Mephit' => 'Ледяной мефит',
            'Imp' => 'Бес',
            'Invisible Stalker' => 'Невидимый охотник',
            'Iron Golem' => 'Железный голем',
            'Jackal' => 'Шакал',
            'Killer Whale' => 'Косатка',
            'Knight' => 'Рыцарь',
            'Lamia' => 'Ламия',
            'Lemure' => 'Лемур',
            'Lizardfolk' => 'Ящеролюд',
            'Mage' => 'Маг',
            'Magma Mephit' => 'Магмовый мефит',
            'Magmin' => 'Магмин',
            'Mammoth' => 'Мамонт',
            'Manticore' => 'Мантикора',
            'Marilith' => 'Марилит',
            'Merfolk' => 'Морской народ',
            'Merrow' => 'Мерроу',
            'Minotaur Skeleton' => 'Скелет минотавра',
            'Mummy Lord' => 'Владыка мумий',
            'Nalfeshnee' => 'Нальфешни',
            'Night Hag' => 'Ночная карга',
            'Nightmare' => 'Кошмар',
            'Noble' => 'Дворянин',
            'Ochre Jelly' => 'Охряная слизь',
            'Ogre Zombie' => 'Зомби-огр',
            'Oni' => 'Они',
            'Otyugh' => 'Отиуг',
            'Owlbear' => 'Совомедведь',
            'Panther' => 'Пантера',
            'Phase Spider' => 'Фазовый паук',
            'Pit Fiend' => 'Исчадие преисподней',
            'Planetar' => 'Планетар',
            'Plesiosaurus' => 'Плезиозавр',
            'Poisonous Snake' => 'Ядовитая змея',
            'Polar Bear' => 'Белый медведь',
            'Priest' => 'Жрец',
            'Pseudodragon' => 'Псевдодракон',
            'Purple Worm' => 'Пурпурный червь',
            'Quasit' => 'Квазит',
            'Quipper' => 'Квиппер',
            'Reef Shark' => 'Рифовая акула',
            'Remorhaz' => 'Реморхаз',
            'Rhinoceros' => 'Носорог',
            'Riding Horse' => 'Верховая лошадь',
            'Roper' => 'Ропер',
            'Rug of Smothering' => 'Ковёр удушения',
            'Rust Monster' => 'Ржавник',
            'Saber-Toothed Tiger' => 'Саблезубый тигр',
            'Sahuagin' => 'Сахуагин',
            'Salamander' => 'Саламандра',
            'Scout' => 'Разведчик',
            'Sea Hag' => 'Морская карга',
            'Sea Horse' => 'Морской конёк',
            'Shadow' => 'Тень',
            'Shambling Mound' => 'Ползучий курган',
            'Shield Guardian' => 'Щитовой страж',
            'Shrieker' => 'Визгун',
            'Solar' => 'Солар',
            'Specter' => 'Спектр',
            'Spirit Naga' => 'Духовная нага',
            'Spy' => 'Шпион',
            'Steam Mephit' => 'Паровой мефит',
            'Stirge' => 'Стирдж',
            'Stone Giant' => 'Каменный великан',
            'Stone Golem' => 'Каменный голем',
            'Storm Giant' => 'Штормовой великан',
            'Succubus/Incubus' => 'Суккуб/инкуб',
            'Tarrasque' => 'Тараск',
            'Thug' => 'Головорез',
            'Tribal Warrior' => 'Воин племени',
            'Triceratops' => 'Трицератопс',
            'Tyrannosaurus Rex' => 'Тираннозавр рекс',
            'Vampire Spawn' => 'Порождение вампира',
            'Vampire, Bat Form' => 'Вампир, облик летучей мыши',
            'Vampire, Mist Form' => 'Вампир, туманный облик',
            'Vampire, Vampire Form' => 'Вампир, истинный облик',
            'Veteran' => 'Ветеран',
            'Violet Fungus' => 'Фиолетовый гриб',
            'Vrock' => 'Врок',
            'Warhorse' => 'Боевой конь',
            'Warhorse Skeleton' => 'Скелет боевого коня',
            'Werebear, Bear Form' => 'Вермедведь, облик медведя',
            'Werebear, Human Form' => 'Вермедведь, облик человека',
            'Werebear, Hybrid Form' => 'Вермедведь, гибридный облик',
            'Wereboar, Boar Form' => 'Веркабан, облик кабана',
            'Wereboar, Human Form' => 'Веркабан, облик человека',
            'Wereboar, Hybrid Form' => 'Веркабан, гибридный облик',
            'Wererat, Human Form' => 'Веркрыса, облик человека',
            'Wererat, Hybrid Form' => 'Веркрыса, гибридный облик',
            'Wererat, Rat Form' => 'Веркрыса, облик крысы',
            'Weretiger, Human Form' => 'Вертигр, облик человека',
            'Weretiger, Hybrid Form' => 'Вертигр, гибридный облик',
            'Weretiger, Tiger Form' => 'Вертигр, облик тигра',
            'Werewolf, Human Form' => 'Вервольф, облик человека',
            'Werewolf, Hybrid Form' => 'Вервольф, гибридный облик',
            'Werewolf, Wolf Form' => 'Вервольф, облик волка',
            'Will-o\'-Wisp' => 'Блуждающий огонёк',
            'Winter Wolf' => 'Зимний волк',
            'Worg' => 'Ворг',
            'Xorn' => 'Зорн',
        ];

        if (isset($direct[$name])) {
            return $direct[$name];
        }

        if (preg_match('/^(.+) Dragon Wyrmling$/', $name, $matches)) {
            return 'Детёныш '.$this->dragonColor($matches[1]).' дракона';
        }

        if (preg_match('/^Swarm of (.+)$/', $name, $matches)) {
            $creature = $this->monsterName($matches[1]);

            return 'Рой: '.mb_strtolower($creature);
        }

        $words = [
            'Adult' => 'Взрослый',
            'Ancient' => 'Древний',
            'Young' => 'Молодой',
            'Black' => 'чёрный',
            'Blue' => 'синий',
            'Brown' => 'бурый',
            'Gray' => 'серый',
            'Brass' => 'латунный',
            'Bronze' => 'бронзовый',
            'Copper' => 'медный',
            'Gold' => 'золотой',
            'Green' => 'зелёный',
            'Red' => 'красный',
            'Silver' => 'серебряный',
            'White' => 'белый',
            'Dragon' => 'дракон',
            'Air' => 'воздушный',
            'Earth' => 'земляной',
            'Fire' => 'огненный',
            'Water' => 'водный',
            'Elemental' => 'элементаль',
            'Giant' => 'гигантский',
            'Dire' => 'лютый',
            'Wolf' => 'волк',
            'Bear' => 'медведь',
            'Badger' => 'барсук',
            'Boar' => 'кабан',
            'Crab' => 'краб',
            'Constrictor' => 'удав',
            'Octopus' => 'осьминог',
            'Weasel' => 'ласка',
            'Rat' => 'крыса',
            'Spider' => 'паук',
            'Scorpion' => 'скорпион',
            'Snake' => 'змея',
            'Eagle' => 'орёл',
            'Elk' => 'лось',
            'Goat' => 'коза',
            'Hyena' => 'гиена',
            'Lizard' => 'ящерица',
            'Owl' => 'сова',
            'Shark' => 'акула',
            'Tiger' => 'тигр',
            'Vulture' => 'стервятник',
            'Wasp' => 'оса',
            'Bat' => 'летучая мышь',
            'Cat' => 'кошка',
            'Frog' => 'лягушка',
            'Horse' => 'лошадь',
            'Mastiff' => 'мастиф',
            'Mule' => 'мул',
            'Pony' => 'пони',
            'Raven' => 'ворон',
            'Camel' => 'верблюд',
            'Crocodile' => 'крокодил',
            'Elephant' => 'слон',
            'Lion' => 'лев',
            'Ape' => 'обезьяна',
            'Basilisk' => 'василиск',
            'Behir' => 'бехир',
            'Bulette' => 'булет',
            'Centaur' => 'кентавр',
            'Chimera' => 'химера',
            'Cyclops' => 'циклоп',
            'Doppelganger' => 'доппельгангер',
            'Dryad' => 'дриада',
            'Gargoyle' => 'горгулья',
            'Ghoul' => 'гуль',
            'Griffon' => 'грифон',
            'Harpy' => 'гарпия',
            'Hydra' => 'гидра',
            'Kraken' => 'кракен',
            'Lich' => 'лич',
            'Medusa' => 'медуза',
            'Mimic' => 'мимик',
            'Minotaur' => 'минотавр',
            'Mummy' => 'мумия',
            'Naga' => 'нага',
            'Ogre' => 'огр',
            'Pegasus' => 'пегас',
            'Rakshasa' => 'ракшаса',
            'Roc' => 'рух',
            'Satyr' => 'сатир',
            'Sphinx' => 'сфинкс',
            'Sprite' => 'спрайт',
            'Treant' => 'энт',
            'Troll' => 'тролль',
            'Unicorn' => 'единорог',
            'Vampire' => 'вампир',
            'Werewolf' => 'вервольф',
            'Wight' => 'умертвие',
            'Wraith' => 'призрак',
            'Wyvern' => 'виверна',
            'Bats' => 'летучих мышей',
            'Beetles' => 'жуков',
            'Centipedes' => 'многоножек',
            'Insects' => 'насекомых',
            'Poisonous' => 'ядовитых',
            'Snakes' => 'змей',
            'Quippers' => 'квипперов',
            'Rats' => 'крыс',
            'Ravens' => 'воронов',
            'Spiders' => 'пауков',
            'Wasps' => 'ос',
            'bats' => 'летучих мышей',
            'beetles' => 'жуков',
            'centipedes' => 'многоножек',
            'insects' => 'насекомых',
            'poisonous' => 'ядовитых',
            'snakes' => 'змей',
            'quippers' => 'квипперов',
            'rats' => 'крыс',
            'ravens' => 'воронов',
            'spiders' => 'пауков',
            'wasps' => 'ос',
        ];

        return preg_replace_callback('/[A-Za-z]+/', fn (array $match): string => $words[$match[0]] ?? $match[0], $name) ?: $name;
    }

    private function abilityName(string $ability): string
    {
        return match (Str::lower($ability)) {
            'str', 'strength' => 'Сила',
            'dex', 'dexterity' => 'Ловкость',
            'con', 'constitution' => 'Телосложение',
            'int', 'intelligence' => 'Интеллект',
            'wis', 'wisdom' => 'Мудрость',
            'cha', 'charisma' => 'Харизма',
            default => $ability,
        };
    }

    private function dictionaryTitle(string $key): string
    {
        return [
            'cp' => 'Медные монеты',
            'sp' => 'Серебряные монеты',
            'ep' => 'Электрумовые монеты',
            'gp' => 'Золотые монеты',
            'pp' => 'Платиновые монеты',
            'walk' => 'Ходьба',
            'fly' => 'Полёт',
            'swim' => 'Плавание',
            'burrow' => 'Копание',
            'climb' => 'Лазание',
            'hover' => 'Парение',
            'passive_perception' => 'Пассивная Внимательность',
            'mundane_items' => 'Обычные предметы',
            'magic_items' => 'Магические предметы',
            'currency' => 'Валюта',
            'lifestyle_expenses' => 'Расходы на образ жизни',
            'selling_guideline' => 'Продажа добычи',
            'short_rest' => 'Короткий отдых',
            'long_rest' => 'Продолжительный отдых',
            'pace' => 'Темп',
            'forced_march' => 'Форсированный марш',
            'difficult_terrain' => 'Сложная местность',
            'navigation' => 'Навигация',
            'building_encounter' => 'Построение столкновения',
            'multipliers' => 'Множители',
            'notes' => 'Заметки',
            'hirelings' => 'Наёмники',
            'loyalty_guideline' => 'Лояльность',
            'version' => 'Версия',
            'levels' => 'Уровни',
            'recovery' => 'Восстановление',
            'currency_units' => 'Валюты',
            'weapon_properties' => 'Свойства оружия',
            'rarities' => 'Редкости',
            'individual' => 'Индивидуальная добыча',
            'hoard' => 'Клады',
            'save_defaults' => 'Спасброски по умолчанию',
            'application' => 'Применение',
            'stacking' => 'Наложение эффектов',
            'safety_notes' => 'Безопасность за столом',
            'severity_roll' => 'Бросок тяжести',
            'minor_roll' => 'Кратковременное безумие',
            'major_roll' => 'Долговременное безумие',
            'chronic_roll' => 'Хроническое безумие',
            'escalation' => 'Эскалация',
            'name_ru' => 'Название',
            'name_en' => 'Название на английском',
            'name' => 'Название',
            'item_type' => 'Тип предмета',
            'subtype' => 'Подтип',
            'cost_gp' => 'Цена',
            'requires_attunement' => 'Настройка',
            'attunement_requirement' => 'Требование настройки',
            'charges' => 'Заряды',
            'destroyed_on_use' => 'Уничтожается при использовании',
            'activation' => 'Использование',
            'type' => 'Тип',
            'study_time_hours' => 'Время изучения',
            'components' => 'Компоненты',
            'material_components' => 'Материальные компоненты',
            'area' => 'Область',
            'shape' => 'Форма',
            'length_ft' => 'Длина',
            'width_ft' => 'Ширина',
            'size_ft' => 'Размер',
            'radius_ft' => 'Радиус',
            'saving_throw' => 'Спасбросок',
            'ability' => 'Характеристика',
            'dc' => 'Сл',
            'success' => 'При успехе',
            'usable_by' => 'Кто может использовать',
            'knowledge_gained' => 'Что даёт изучение',
            'learnable' => 'Можно изучить',
            'dm_adjudication_required' => 'Требует решения ведущего',
            'one_use_spell' => 'Одноразовое заклинание',
            'spellcasting_required' => 'Нужно умение творить заклинания',
            'counterspell_interaction' => 'Можно контрзаклинание',
            'dispel_magic_interaction' => 'Взаимодействует с рассеиванием магии',
            'gp_value' => 'Стоимость в золотых',
            'value_gp' => 'Цена',
            'roll' => 'Бросок',
            'unit' => 'Единица',
            'count' => 'Количество',
            'table' => 'Таблица',
            'tier_1' => 'Уровни 1-4',
            'tier_2' => 'Уровни 5-10',
            'tier_3' => 'Уровни 11-16',
            'tier_4' => 'Уровни 17-20',
            'works_for' => 'Подходит для',
            'minor' => 'кратковременного безумия',
            'major' => 'долговременного безумия',
            'chronic' => 'хронического безумия',
            'calm_emotions' => 'Успокоение эмоций',
            'greater_restoration' => 'Высшее восстановление',
            'ending_duration' => 'Окончание длительности',
            'severity' => 'Тяжесть',
            'Artisan\'s Tools' => 'Ремесленные инструменты',
            'Armor' => 'Доспехи',
            'Weapons' => 'Оружие',
            'Simple Weapons' => 'Простое оружие',
            'Martial Weapons' => 'Воинское оружие',
            'Other' => 'Другое',
            'Uncommon' => 'необычный',
            'Common' => 'обычный',
            'Rare' => 'редкий',
            'Very Rare' => 'очень редкий',
            'Legendary' => 'легендарный',
            'Artifact' => 'артефакт',
        ][$key] ?? Str::of($key)->replace(['_', '-'], ' ')->title()->toString();
    }

    private function readJson(string $relativePath, mixed $default): mixed
    {
        return $this->readJsonFromBase($this->basePath(), $relativePath, $default);
    }

    private function readJsonFromBase(string $base, string $relativePath, mixed $default): mixed
    {
        $path = $base.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (! is_file($path)) {
            return $default;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return $default;
        }

        $decoded = json_decode($contents, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }

    private function cacheKey(string $suffix): string
    {
        return 'dnd5e_data.'.self::CACHE_SCHEMA_VERSION.'.'.md5($this->basePath().'|'.$this->manualBasePath()).'.'.$this->dataVersion().'.'.$suffix;
    }

    private function dataVersion(): string
    {
        $paths = array_filter([$this->basePath(), $this->manualBasePath()], 'is_dir');
        $fingerprint = [];

        foreach ($paths as $path) {
            $fingerprint[] = $path.'|'.filemtime($path);

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() === 'json') {
                    $fingerprint[] = $file->getPathname().'|'.$file->getMTime().'|'.$file->getSize();
                }
            }
        }

        sort($fingerprint);

        return md5(implode(';', $fingerprint));
    }
}
