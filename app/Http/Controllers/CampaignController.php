<?php

namespace App\Http\Controllers;

use App\Support\DmgDataRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request, DmgDataRepository $data): View
    {
        $options = [
            'mode' => $request->query('mode', 'hoard'),
            'danger' => $request->query('danger', '0_4'),
            'focus' => $request->query('focus', 'any'),
            'count' => (int) $request->query('count', 4),
        ];

        $options['mode'] = in_array($options['mode'], array_keys($this->modes()), true)
            ? $options['mode']
            : 'hoard';
        $options['danger'] = in_array($options['danger'], array_keys($this->dangerTiers()), true)
            ? $options['danger']
            : '0_4';
        $options['focus'] = in_array($options['focus'], array_keys($this->focuses()), true)
            ? $options['focus']
            : 'any';
        $options['count'] = min(12, max(1, $options['count']));

        return view('campaigns.index', [
            'modes' => $this->modes(),
            'dangerTiers' => $this->dangerTiers(),
            'focuses' => $this->focuses(),
            'options' => $options,
            'result' => $request->has('generate') ? $this->generate($data, $options) : null,
        ]);
    }

    private function generate(DmgDataRepository $data, array $options): array
    {
        return match ($options['mode']) {
            'individual' => $this->generateIndividualTreasure($options),
            'hoard' => $this->generateHoardTreasure($data, $options),
            default => $this->generateItems($data, $options),
        };
    }

    private function generateIndividualTreasure(array $options): array
    {
        $roll = random_int(1, 100);
        $row = $this->findRollRow($this->individualTreasureTables()[$options['danger']], $roll);

        return [
            'mode' => 'individual',
            'roll' => $roll,
            'coins' => $this->rollCoinSet($row['coins']),
            'valuables' => [],
            'items' => [],
            'magic_requests' => [],
            'note' => null,
        ];
    }

    private function generateHoardTreasure(DmgDataRepository $data, array $options): array
    {
        $roll = random_int(1, 100);
        $tier = $this->hoardTreasureTables()[$options['danger']];
        $row = $this->findRollRow($tier['rows'], $roll);

        $valuables = [];

        foreach ($row['valuables'] ?? [] as $request) {
            $valuables = [
                ...$valuables,
                ...$this->pickValuables($data, $request['type'], $request['value_gp'], $this->rollExpression($request['count'])),
            ];
        }

        $items = [];

        foreach ($row['magic'] ?? [] as $request) {
            $amount = $this->rollExpression($request['count']);
            $items = [
                ...$items,
                ...$this->pickMagicItems($data, $request['table'], $amount),
            ];
        }

        return [
            'mode' => 'hoard',
            'roll' => $roll,
            'coins' => $this->rollCoinSet($tier['coins']),
            'valuables' => $valuables,
            'items' => $items,
            'magic_requests' => $row['magic'] ?? [],
            'note' => null,
        ];
    }

    private function generateItems(DmgDataRepository $data, array $options): array
    {
        $sources = $this->sourcesFor($options['focus'], $options['danger']);
        $pool = collect($sources)
            ->flatMap(function (string $slug) use ($data): array {
                return collect($data->entries($slug))
                    ->map(fn (array $entry): array => [
                        'name' => $entry['name'],
                        'category' => $entry['category_title'],
                        'type' => $entry['type'] ?: $entry['item_group'] ?: $entry['category_title'],
                        'rarity' => $entry['rarity'] ?: null,
                        'cost' => $entry['cost'] ?: ($entry['value_gp'] ? $entry['value_gp'].' зм' : null),
                        'description' => $entry['excerpt'] ?: $entry['description'],
                        'url' => route('data.entity', [$entry['category_slug'], $entry['index']]),
                        'category_slug' => $entry['category_slug'],
                    ])
                    ->all();
            })
            ->filter(fn (array $entry): bool => $this->allowedForDanger($entry, $options['danger']))
            ->values();

        if ($pool->isEmpty()) {
            return [
                'mode' => 'items',
                'roll' => null,
                'coins' => $this->coinBundle($options['danger']),
                'valuables' => [],
                'items' => [],
                'magic_requests' => [],
                'note' => 'Для выбранных параметров пока нет подходящих предметов.',
            ];
        }

        $items = [];
        $used = [];

        for ($i = 0; $i < $options['count']; $i++) {
            $available = $pool->reject(fn (array $entry): bool => in_array($entry['category_slug'].'|'.$entry['name'], $used, true))->values();

            if ($available->isEmpty()) {
                $available = $pool;
                $used = [];
            }

            $entry = $available[random_int(0, $available->count() - 1)];
            $used[] = $entry['category_slug'].'|'.$entry['name'];
            $items[] = $entry;
        }

        return [
            'mode' => 'items',
            'roll' => null,
            'coins' => $this->coinBundle($options['danger']),
            'valuables' => [],
            'items' => $items,
            'magic_requests' => [],
            'note' => null,
        ];
    }

    private function findRollRow(array $rows, int $roll): array
    {
        foreach ($rows as $row) {
            if ($roll >= $row['min'] && $roll <= $row['max']) {
                return $row;
            }
        }

        return $rows[array_key_last($rows)];
    }

    private function rollCoinSet(array $coins): array
    {
        $labels = [
            'cp' => 'Медные монеты',
            'sp' => 'Серебряные монеты',
            'ep' => 'Электрумовые монеты',
            'gp' => 'Золотые монеты',
            'pp' => 'Платиновые монеты',
        ];

        return collect($coins)
            ->mapWithKeys(fn (array $coin): array => [
                $labels[$coin['unit']] ?? $coin['unit'] => $this->rollExpression($coin['roll']).' '.$this->coinUnit($coin['unit']),
            ])
            ->all();
    }

    private function pickValuables(DmgDataRepository $data, string $type, int $value, int $count): array
    {
        $slug = $type === 'art' ? 'art_objects' : 'gems';
        $pool = collect($data->entries($slug))
            ->filter(fn (array $entry): bool => (int) ($entry['value_gp'] ?? 0) === $value)
            ->values();

        if ($pool->isEmpty()) {
            return [[
                'name' => ($type === 'art' ? 'Предмет искусства' : 'Самоцвет').' на '.$value.' зм',
                'category' => $type === 'art' ? 'Предметы искусства' : 'Самоцветы',
                'type' => 'Ценность',
                'rarity' => null,
                'cost' => $value.' зм',
                'description' => null,
                'url' => null,
                'quantity' => $count,
            ]];
        }

        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $entry = $pool[random_int(0, $pool->count() - 1)];
            $items[] = $this->entryCard($entry, $value.' зм');
        }

        return $this->compactCards($items);
    }

    private function pickMagicItems(DmgDataRepository $data, string $table, int $count): array
    {
        $pool = collect($this->magicSourcesForTable($table))
            ->flatMap(fn (string $slug): array => $data->entries($slug))
            ->filter(fn (array $entry): bool => $this->magicEntryAllowedForTable($entry, $table))
            ->values();

        if ($pool->isEmpty()) {
            return [[
                'name' => 'Предмет из магической таблицы '.$table,
                'category' => 'Магические предметы',
                'type' => 'Таблица '.$table,
                'rarity' => null,
                'cost' => null,
                'description' => 'Нужно заполнить точную таблицу магических предметов '.$table.'.',
                'url' => null,
            ]];
        }

        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $entry = $pool[random_int(0, $pool->count() - 1)];
            $items[] = [
                ...$this->entryCard($entry),
                'type' => 'Таблица '.$table.' · '.($entry['type'] ?: $entry['item_group'] ?: $entry['category_title']),
            ];
        }

        return $this->compactCards($items);
    }

    private function compactCards(array $items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $key = implode('|', [
                $item['url'] ?? '',
                $item['category'] ?? '',
                $item['name'] ?? '',
                $item['type'] ?? '',
                $item['rarity'] ?? '',
                $item['cost'] ?? '',
            ]);

            if (! isset($grouped[$key])) {
                $item['quantity'] = $item['quantity'] ?? 1;
                $grouped[$key] = $item;
                continue;
            }

            $grouped[$key]['quantity'] = ($grouped[$key]['quantity'] ?? 1) + ($item['quantity'] ?? 1);
        }

        return array_values($grouped);
    }

    private function entryCard(array $entry, ?string $fallbackCost = null): array
    {
        return [
            'name' => $entry['name'],
            'category' => $entry['category_title'],
            'type' => $entry['type'] ?: $entry['item_group'] ?: $entry['category_title'],
            'rarity' => $entry['rarity'] ?: null,
            'cost' => $entry['cost'] ?: ($entry['value_gp'] ? $entry['value_gp'].' зм' : $fallbackCost),
            'description' => $entry['excerpt'] ?: $entry['description'],
            'url' => route('data.entity', [$entry['category_slug'], $entry['index']]),
        ];
    }

    private function magicSourcesForTable(string $table): array
    {
        return match ($table) {
            'А', 'Б' => ['potions', 'scrolls', 'magic_items_srd'],
            'В', 'Г' => ['potions', 'scrolls', 'magic_weapons_armor', 'magic_items_srd'],
            'Д', 'Е', 'Ё' => ['scrolls', 'magic_weapons_armor', 'magic_items_srd'],
            default => ['magic_weapons_armor', 'magic_items_srd', 'artifacts_srd'],
        };
    }

    private function magicEntryAllowedForTable(array $entry, string $table): bool
    {
        $rarity = mb_strtolower((string) ($entry['rarity'] ?? ''));

        return match ($table) {
            'А' => $this->rarityIn($rarity, ['обычный', 'необычный', 'common', 'uncommon', '']),
            'Б', 'В' => ! $this->rarityIn($rarity, ['очень редкий', 'легендарный', 'артефакт', 'very rare', 'legendary', 'artifact']),
            'Г', 'Д' => ! $this->rarityIn($rarity, ['артефакт', 'artifact']),
            'Е', 'Ё' => ! $this->rarityIn($rarity, ['обычный', 'common']),
            default => true,
        };
    }

    private function rollExpression(string $expression): int
    {
        if (is_numeric($expression)) {
            return (int) $expression;
        }

        if (! preg_match('/^(\d+)d(\d+)(?:x(\d+))?$/', $expression, $matches)) {
            return 0;
        }

        $total = 0;

        for ($i = 0; $i < (int) $matches[1]; $i++) {
            $total += random_int(1, (int) $matches[2]);
        }

        return $total * (int) ($matches[3] ?? 1);
    }

    private function coinUnit(string $unit): string
    {
        return [
            'cp' => 'мм',
            'sp' => 'см',
            'ep' => 'эм',
            'gp' => 'зм',
            'pp' => 'пм',
        ][$unit] ?? $unit;
    }

    private function individualTreasureTables(): array
    {
        return [
            '0_4' => [
                $this->treasureRow(1, 30, coins: [['roll' => '5d6', 'unit' => 'cp']]),
                $this->treasureRow(31, 60, coins: [['roll' => '4d6', 'unit' => 'sp']]),
                $this->treasureRow(61, 70, coins: [['roll' => '3d6', 'unit' => 'ep']]),
                $this->treasureRow(71, 95, coins: [['roll' => '3d6', 'unit' => 'gp']]),
                $this->treasureRow(96, 100, coins: [['roll' => '1d6', 'unit' => 'pp']]),
            ],
            '5_10' => [
                $this->treasureRow(1, 30, coins: [['roll' => '4d6x100', 'unit' => 'cp'], ['roll' => '1d6x10', 'unit' => 'ep']]),
                $this->treasureRow(31, 60, coins: [['roll' => '6d6x10', 'unit' => 'sp'], ['roll' => '2d6x10', 'unit' => 'gp']]),
                $this->treasureRow(61, 70, coins: [['roll' => '3d6x10', 'unit' => 'ep'], ['roll' => '2d6x10', 'unit' => 'gp']]),
                $this->treasureRow(71, 95, coins: [['roll' => '4d6x10', 'unit' => 'gp']]),
                $this->treasureRow(96, 100, coins: [['roll' => '2d6x10', 'unit' => 'gp'], ['roll' => '3d6', 'unit' => 'pp']]),
            ],
            '11_16' => [
                $this->treasureRow(1, 20, coins: [['roll' => '4d6x100', 'unit' => 'sp'], ['roll' => '1d6x100', 'unit' => 'gp']]),
                $this->treasureRow(21, 35, coins: [['roll' => '1d6x100', 'unit' => 'ep'], ['roll' => '1d6x100', 'unit' => 'gp']]),
                $this->treasureRow(36, 75, coins: [['roll' => '2d6x100', 'unit' => 'gp'], ['roll' => '1d6x10', 'unit' => 'pp']]),
                $this->treasureRow(76, 100, coins: [['roll' => '2d6x100', 'unit' => 'gp'], ['roll' => '2d6x10', 'unit' => 'pp']]),
            ],
            '17_plus' => [
                $this->treasureRow(1, 15, coins: [['roll' => '2d6x1000', 'unit' => 'ep'], ['roll' => '8d6x100', 'unit' => 'gp']]),
                $this->treasureRow(16, 55, coins: [['roll' => '1d6x1000', 'unit' => 'gp'], ['roll' => '1d6x100', 'unit' => 'pp']]),
                $this->treasureRow(56, 100, coins: [['roll' => '1d6x1000', 'unit' => 'gp'], ['roll' => '2d6x100', 'unit' => 'pp']]),
            ],
        ];
    }

    private function hoardTreasureTables(): array
    {
        return [
            '0_4' => [
                'coins' => [['roll' => '6d6x100', 'unit' => 'cp'], ['roll' => '3d6x100', 'unit' => 'sp'], ['roll' => '2d6x10', 'unit' => 'gp']],
                'rows' => [
                    $this->treasureRow(1, 6),
                    $this->treasureRow(7, 16, valuables: [$this->valuable('gem', 10, '2d6')]),
                    $this->treasureRow(17, 26, valuables: [$this->valuable('art', 25, '2d4')]),
                    $this->treasureRow(27, 36, valuables: [$this->valuable('gem', 50, '2d6')]),
                    $this->treasureRow(37, 44, valuables: [$this->valuable('gem', 10, '2d6')], magic: [$this->magic('А', '1d6')]),
                    $this->treasureRow(45, 52, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('А', '1d6')]),
                    $this->treasureRow(53, 60, valuables: [$this->valuable('gem', 50, '2d6')], magic: [$this->magic('А', '1d6')]),
                    $this->treasureRow(61, 65, valuables: [$this->valuable('gem', 10, '2d6')], magic: [$this->magic('Б', '1d4')]),
                    $this->treasureRow(66, 70, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('Б', '1d4')]),
                    $this->treasureRow(71, 75, valuables: [$this->valuable('gem', 50, '2d6')], magic: [$this->magic('Б', '1d4')]),
                    $this->treasureRow(76, 78, valuables: [$this->valuable('gem', 10, '2d6')], magic: [$this->magic('В', '1d4')]),
                    $this->treasureRow(79, 80, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('В', '1d4')]),
                    $this->treasureRow(81, 85, valuables: [$this->valuable('gem', 50, '2d6')], magic: [$this->magic('В', '1d4')]),
                    $this->treasureRow(86, 92, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('Г', '1d4')]),
                    $this->treasureRow(93, 97, valuables: [$this->valuable('gem', 50, '2d6')], magic: [$this->magic('Е', '1d4')]),
                    $this->treasureRow(98, 99, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('Ё', '1')]),
                    $this->treasureRow(100, 100, valuables: [$this->valuable('gem', 50, '2d6')], magic: [$this->magic('Ё', '1')]),
                ],
            ],
            '5_10' => [
                'coins' => [['roll' => '2d6x100', 'unit' => 'cp'], ['roll' => '2d6x1000', 'unit' => 'sp'], ['roll' => '6d6x100', 'unit' => 'gp'], ['roll' => '3d6x10', 'unit' => 'pp']],
                'rows' => [
                    $this->treasureRow(1, 4),
                    $this->treasureRow(5, 10, valuables: [$this->valuable('art', 25, '2d4')]),
                    $this->treasureRow(11, 16, valuables: [$this->valuable('gem', 50, '3d6')]),
                    $this->treasureRow(17, 22, valuables: [$this->valuable('gem', 100, '3d6')]),
                    $this->treasureRow(23, 28, valuables: [$this->valuable('art', 250, '2d4')]),
                    $this->treasureRow(29, 32, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('А', '1d6')]),
                    $this->treasureRow(33, 36, valuables: [$this->valuable('gem', 50, '3d6')], magic: [$this->magic('А', '1d6')]),
                    $this->treasureRow(37, 40, valuables: [$this->valuable('gem', 100, '3d6')], magic: [$this->magic('А', '1d6')]),
                    $this->treasureRow(41, 44, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('А', '1d6')]),
                    $this->treasureRow(45, 49, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('Б', '1d4')]),
                    $this->treasureRow(50, 54, valuables: [$this->valuable('gem', 50, '3d6')], magic: [$this->magic('Б', '1d4')]),
                    $this->treasureRow(55, 59, valuables: [$this->valuable('gem', 100, '3d6')], magic: [$this->magic('Б', '1d4')]),
                    $this->treasureRow(60, 63, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('Б', '1d4')]),
                    $this->treasureRow(64, 66, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('В', '1d4')]),
                    $this->treasureRow(67, 69, valuables: [$this->valuable('gem', 50, '3d6')], magic: [$this->magic('В', '1d4')]),
                    $this->treasureRow(70, 72, valuables: [$this->valuable('gem', 100, '3d6')], magic: [$this->magic('В', '1d4')]),
                    $this->treasureRow(73, 74, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('В', '1d4')]),
                    $this->treasureRow(75, 76, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('Г', '1')]),
                    $this->treasureRow(77, 78, valuables: [$this->valuable('gem', 50, '3d6')], magic: [$this->magic('Г', '1')]),
                    $this->treasureRow(79, 79, valuables: [$this->valuable('gem', 100, '3d6')], magic: [$this->magic('Г', '1')]),
                    $this->treasureRow(80, 80, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('Г', '1')]),
                    $this->treasureRow(81, 84, valuables: [$this->valuable('art', 25, '2d4')], magic: [$this->magic('Е', '1d4')]),
                    $this->treasureRow(85, 88, valuables: [$this->valuable('gem', 50, '3d6')], magic: [$this->magic('Е', '1d4')]),
                    $this->treasureRow(89, 91, valuables: [$this->valuable('gem', 100, '3d6')], magic: [$this->magic('Е', '1d4')]),
                    $this->treasureRow(92, 94, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('Е', '1d4')]),
                    $this->treasureRow(95, 96, valuables: [$this->valuable('gem', 100, '3d6')], magic: [$this->magic('Ё', '1d4')]),
                    $this->treasureRow(97, 98, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('Ё', '1d4')]),
                    $this->treasureRow(99, 99, valuables: [$this->valuable('gem', 100, '3d6')], magic: [$this->magic('Ж', '1')]),
                    $this->treasureRow(100, 100, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('Ж', '1')]),
                ],
            ],
            '11_16' => [
                'coins' => [['roll' => '4d6x1000', 'unit' => 'gp'], ['roll' => '5d6x100', 'unit' => 'pp']],
                'rows' => [
                    $this->treasureRow(1, 3),
                    $this->treasureRow(4, 6, valuables: [$this->valuable('art', 250, '2d4')]),
                    $this->treasureRow(7, 9, valuables: [$this->valuable('art', 750, '2d4')]),
                    $this->treasureRow(10, 12, valuables: [$this->valuable('gem', 500, '3d6')]),
                    $this->treasureRow(13, 15, valuables: [$this->valuable('gem', 1000, '3d6')]),
                    ...$this->hoardRowsWithPairs(16, 29, [['art', 250], ['art', 750], ['gem', 500], ['gem', 1000]], [$this->magic('А', '1d4'), $this->magic('Б', '1d6')]),
                    ...$this->hoardRowsWithPairs(30, 50, [['art', 250], ['art', 750], ['gem', 500], ['gem', 1000]], [$this->magic('В', '1d6')], [6, 5, 5, 5]),
                    ...$this->hoardRowsWithPairs(51, 66, [['art', 250], ['art', 750], ['gem', 500], ['gem', 1000]], [$this->magic('Г', '1d4')]),
                    ...$this->hoardRowsWithPairs(67, 74, [['art', 250], ['art', 750], ['gem', 500], ['gem', 1000]], [$this->magic('Д', '1')], [2, 2, 2, 2]),
                    ...$this->hoardRowsWithPairs(75, 82, [['art', 250], ['art', 750], ['gem', 500], ['gem', 1000]], [$this->magic('Е', '1'), $this->magic('Ё', '1d4')], [2, 2, 2, 2]),
                    $this->treasureRow(83, 88, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('Ж', '1d4')]),
                    $this->treasureRow(89, 90, valuables: [$this->valuable('art', 750, '2d4')], magic: [$this->magic('Ж', '1d4')]),
                    $this->treasureRow(91, 92, valuables: [$this->valuable('gem', 1000, '3d6')], magic: [$this->magic('Ж', '1d4')]),
                    $this->treasureRow(93, 94, valuables: [$this->valuable('art', 250, '2d4')], magic: [$this->magic('З', '1')]),
                    $this->treasureRow(95, 96, valuables: [$this->valuable('art', 750, '2d4')], magic: [$this->magic('З', '1')]),
                    $this->treasureRow(97, 98, valuables: [$this->valuable('gem', 500, '3d6')], magic: [$this->magic('З', '1')]),
                    $this->treasureRow(99, 100, valuables: [$this->valuable('gem', 1000, '3d6')], magic: [$this->magic('З', '1')]),
                ],
            ],
            '17_plus' => [
                'coins' => [['roll' => '12d6x1000', 'unit' => 'gp'], ['roll' => '8d6x1000', 'unit' => 'pp']],
                'rows' => [
                    $this->treasureRow(1, 2),
                    ...$this->hoardRowsWithPairs(3, 14, [['gem', 1000, '3d6'], ['art', 2500, '1d10'], ['art', 7500, '1d4'], ['gem', 5000, '1d8']], [$this->magic('В', '1d8')], [3, 3, 3, 3]),
                    ...$this->hoardRowsWithPairs(15, 46, [['gem', 1000, '3d6'], ['art', 2500, '1d10'], ['art', 7500, '1d4'], ['gem', 5000, '1d8']], [$this->magic('Г', '1d6')], [8, 8, 8, 8]),
                    ...$this->hoardRowsWithPairs(47, 68, [['gem', 1000, '3d6'], ['art', 2500, '1d10'], ['art', 7500, '1d4'], ['gem', 5000, '1d8']], [$this->magic('Д', '1d6')], [6, 6, 5, 5]),
                    ...$this->hoardRowsWithPairs(69, 72, [['gem', 1000, '3d6'], ['art', 2500, '1d10'], ['art', 7500, '1d4'], ['gem', 5000, '1d8']], [$this->magic('Е', '1d4')], [1, 1, 1, 1]),
                    ...$this->hoardRowsWithPairs(73, 80, [['gem', 1000, '3d6'], ['art', 2500, '1d10'], ['art', 7500, '1d4'], ['gem', 5000, '1d8']], [$this->magic('Ж', '1d4')], [2, 2, 2, 2]),
                    ...$this->hoardRowsWithPairs(81, 100, [['gem', 1000, '3d6'], ['art', 2500, '1d10'], ['art', 7500, '1d4'], ['gem', 5000, '1d8']], [$this->magic('З', '1d4')], [5, 5, 5, 5]),
                ],
            ],
        ];
    }

    private function treasureRow(int $min, int $max, array $coins = [], array $valuables = [], array $magic = []): array
    {
        return compact('min', 'max', 'coins', 'valuables', 'magic');
    }

    private function valuable(string $type, int $value, string $count): array
    {
        return ['type' => $type, 'value_gp' => $value, 'count' => $count];
    }

    private function magic(string $table, string $count): array
    {
        return ['table' => $table, 'count' => $count];
    }

    private function hoardRowsWithPairs(int $start, int $end, array $valuables, array $magic, ?array $widths = null): array
    {
        $rows = [];
        $current = $start;
        $widths ??= array_fill(0, count($valuables), (int) floor(($end - $start + 1) / count($valuables)));
        $remaining = ($end - $start + 1) - array_sum($widths);

        for ($i = 0; $i < $remaining; $i++) {
            $widths[$i] = ($widths[$i] ?? 0) + 1;
        }

        foreach ($valuables as $index => $valuable) {
            $width = $widths[$index] ?? 1;
            $count = $valuable[2] ?? ($valuable[0] === 'art' ? '2d4' : '3d6');
            $rows[] = $this->treasureRow(
                $current,
                min($end, $current + $width - 1),
                valuables: [$this->valuable($valuable[0], $valuable[1], $count)],
                magic: $magic
            );
            $current += $width;
        }

        return $rows;
    }

    private function sourcesFor(string $focus, string $danger): array
    {
        $sources = [
            'knowledge' => ['books', 'scrolls'],
            'consumables' => ['potions', 'scrolls', 'poisons'],
            'equipment' => ['weapons', 'armor', 'adventuring_gear', 'tools', 'mounts', 'vehicles', 'trade_goods'],
            'magic' => ['magic_weapons_armor', 'magic_items_srd'],
            'valuables' => ['gems', 'jewelry', 'art_objects', 'rare_goods', 'relics'],
            'any' => ['books', 'scrolls', 'potions', 'poisons', 'weapons', 'armor', 'adventuring_gear', 'tools', 'mounts', 'vehicles', 'trade_goods', 'magic_weapons_armor', 'magic_items_srd', 'gems', 'jewelry', 'art_objects', 'rare_goods', 'mundane_loot'],
        ][$focus] ?? ['books', 'scrolls', 'potions'];

        if ($danger === '17_plus') {
            $sources[] = 'artifacts_srd';
            $sources[] = 'relics';
        }

        return array_values(array_unique($sources));
    }

    private function allowedForDanger(array $entry, string $danger): bool
    {
        $rarity = mb_strtolower((string) ($entry['rarity'] ?? ''));
        $cost = $this->costValue($entry['cost'] ?? null);

        return match ($danger) {
            '0_4' => $this->rarityIn($rarity, ['обычный', 'необычный', 'common', 'uncommon', '']) && ($cost === null || $cost <= 500),
            '5_10' => ! $this->rarityIn($rarity, ['легендарный', 'артефакт', 'legendary', 'artifact']) && ($cost === null || $cost <= 5000),
            '11_16' => ! $this->rarityIn($rarity, ['артефакт', 'artifact']) && ($cost === null || $cost <= 25000),
            default => true,
        };
    }

    private function rarityIn(string $rarity, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle === '' && $rarity === '') {
                return true;
            }

            if ($needle !== '' && str_contains($rarity, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function costValue(?string $cost): ?int
    {
        if ($cost === null || $cost === '') {
            return null;
        }

        return preg_match('/\d+/u', $cost, $matches) ? (int) $matches[0] : null;
    }

    private function coinBundle(string $danger): array
    {
        return match ($danger) {
            '0_4' => ['Мелочь' => $this->roll('5d6').' мм', 'Серебро' => $this->roll('4d6').' см'],
            '5_10' => ['Серебро' => $this->roll('4d6') * 10 .' см', 'Золото' => $this->roll('2d6') * 10 .' зм'],
            '11_16' => ['Золото' => $this->roll('4d6') * 100 .' зм', 'Платина' => $this->roll('1d6') * 100 .' пм'],
            default => ['Золото' => $this->roll('12d6') * 1000 .' зм', 'Платина' => $this->roll('8d6') * 1000 .' пм'],
        };
    }

    private function roll(string $dice): int
    {
        if (! preg_match('/^(\d+)d(\d+)$/', $dice, $matches)) {
            return 0;
        }

        $total = 0;

        for ($i = 0; $i < (int) $matches[1]; $i++) {
            $total += random_int(1, (int) $matches[2]);
        }

        return $total;
    }

    private function dangerTiers(): array
    {
        return [
            '0_4' => 'ПО 0-4',
            '5_10' => 'ПО 5-10',
            '11_16' => 'ПО 11-16',
            '17_plus' => 'ПО 17+',
        ];
    }

    private function focuses(): array
    {
        return [
            'any' => 'Любые предметы',
            'equipment' => 'Оружие, доспехи и снаряжение',
            'knowledge' => 'Книги и свитки',
            'consumables' => 'Зелья, яды и свитки',
            'magic' => 'Магические предметы',
            'valuables' => 'Ценности и сокровища',
        ];
    }

    private function modes(): array
    {
        return [
            'hoard' => 'Сокровищница к100',
            'individual' => 'Индивидуальные сокровища к100',
            'items' => 'Быстрые случайные предметы',
        ];
    }
}
