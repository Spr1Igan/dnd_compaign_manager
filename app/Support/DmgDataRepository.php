<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DmgDataRepository
{
    private const CACHE_TTL = 3600;

    private const CATEGORIES = [
        'magic_items' => [
            'title' => 'Магические предметы',
            'description' => 'Предметы, зелья, свитки, оружие, доспехи и чудесные находки из Руководства Мастера.',
        ],
        'artifacts' => [
            'title' => 'Артефакты',
            'description' => 'Легендарные артефакты, их свойства и примеры использования в кампании.',
        ],
        'traps' => [
            'title' => 'Ловушки',
            'description' => 'Правила обнаружения, сложности, урон и примеры ловушек.',
        ],
        'poisons' => [
            'title' => 'Яды',
            'description' => 'Яды, способы применения, эффекты и правила получения.',
        ],
        'diseases' => [
            'title' => 'Болезни',
            'description' => 'Заболевания, симптомы, последствия и лечение.',
        ],
        'madness' => [
            'title' => 'Безумие',
            'description' => 'Кратковременное, долговременное и бессрочное безумие.',
        ],
        'siege_equipment' => [
            'title' => 'Осадное снаряжение',
            'description' => 'Баллисты, пушки, осадные башни и другие крупные устройства.',
        ],
        'optional_rules' => [
            'title' => 'Опциональные правила',
            'description' => 'Дополнительные варианты правил и мастерские настройки игры.',
        ],
    ];

    public function basePath(): string
    {
        return base_path('some_data/dmg_structured_export');
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
            ->map(function (array $category, string $slug): array {
                return [
                    ...$category,
                    'slug' => $slug,
                    'count' => count($this->entries($slug)),
                ];
            })
            ->all();
    }

    public function category(string $slug): ?array
    {
        if (! array_key_exists($slug, self::CATEGORIES)) {
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
        if (! array_key_exists($slug, self::CATEGORIES)) {
            return [];
        }

        return Cache::remember($this->cacheKey("entities.{$slug}"), self::CACHE_TTL, function () use ($slug): array {
            $items = $this->readManualJson("entities/{$slug}.json", null);
            $isManual = is_array($items);

            if (! $isManual) {
                $items = $this->readJson("entities/{$slug}.json", []);
            }

            if (! is_array($items)) {
                return [];
            }

            return collect($items)
                ->values()
                ->map(fn (mixed $item, int $index): array => $this->normalizeEntity((array) $item, $slug, $index, $isManual))
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

        $categorySlugs = $category && array_key_exists($category, self::CATEGORIES)
            ? [$category]
            : array_keys(self::CATEGORIES);

        return collect($categorySlugs)
            ->flatMap(fn (string $slug): array => $this->entries($slug))
            ->filter(function (array $entry) use ($query): bool {
                $haystack = mb_strtolower($entry['name'].' '.$entry['description'].' '.implode(' ', $entry['tags']));
                return str_contains($haystack, mb_strtolower($query));
            })
            ->take(80)
            ->values()
            ->all();
    }

    public function chapters(): array
    {
        $toc = $this->readJson('toc.json', []);

        if (! is_array($toc)) {
            return [];
        }

        return collect($toc)
            ->filter(fn (mixed $chapter): bool => is_array($chapter) && isset($chapter['id'], $chapter['title'], $chapter['file']))
            ->map(fn (array $chapter): array => [
                'id' => (string) $chapter['id'],
                'title' => (string) $chapter['title'],
                'file' => (string) $chapter['file'],
                'page_pdf_start' => $chapter['page_pdf_start'] ?? null,
                'page_pdf_end' => $chapter['page_pdf_end'] ?? null,
            ])
            ->values()
            ->all();
    }

    public function chapter(string $id): ?array
    {
        $chapter = collect($this->chapters())->firstWhere('id', $id);

        if (! $chapter) {
            return null;
        }

        $file = basename((string) $chapter['file']);
        $data = $this->readJson("chapters/{$file}", null);

        if (! is_array($data)) {
            return null;
        }

        return [
            ...$chapter,
            ...$data,
        ];
    }

    public function tableGroups(): array
    {
        return Cache::remember($this->cacheKey('tables.index'), self::CACHE_TTL, function (): array {
            $path = $this->basePath().DIRECTORY_SEPARATOR.'tables';

            if (! is_dir($path)) {
                return [];
            }

            $files = glob($path.DIRECTORY_SEPARATOR.'*.json') ?: [];

            return collect($files)
                ->map(function (string $file): array {
                    $data = $this->readJson('tables/'.basename($file), []);
                    $rows = is_array($data['rows'] ?? null) ? $data['rows'] : [];
                    $pages = collect($rows)->pluck('page_pdf')->filter()->unique()->values()->all();

                    return [
                        'id' => pathinfo($file, PATHINFO_FILENAME),
                        'section' => (string) ($data['section'] ?? pathinfo($file, PATHINFO_FILENAME)),
                        'rows_count' => count($rows),
                        'pages' => $pages,
                        'sample_rows' => array_slice($rows, 0, 10),
                    ];
                })
                ->sortBy('section')
                ->values()
                ->all();
        });
    }

    private function normalizeEntity(array $item, string $slug, int $index, bool $isManual): array
    {
        $effect = $this->normalizeTextList($item['effect'] ?? []);
        $description = $this->flattenText($item['description'] ?? '');

        if ($description === '' && $effect !== []) {
            $description = implode(' ', $effect);
        }

        $name = trim($this->flattenText($item['name'] ?? ''));
        $source = is_array($item['source'] ?? null) ? $item['source'] : [];

        return [
            'index' => $index,
            'category_slug' => $slug,
            'category_title' => self::CATEGORIES[$slug]['title'],
            'name' => $name !== '' ? $name : 'Запись '.($index + 1),
            'type' => $this->flattenText($item['type'] ?? ''),
            'price_gp' => $item['price_gp'] ?? null,
            'saving_throw' => $this->flattenText($item['saving_throw'] ?? ''),
            'dc' => $item['dc'] ?? null,
            'attack_bonus' => $item['attack_bonus'] ?? null,
            'page_pdf' => $source['page_pdf'] ?? $item['page_pdf'] ?? null,
            'description' => $description,
            'effect' => $effect,
            'sections' => $this->manualSections($item),
            'tags' => $this->normalizeTextList($item['tags'] ?? []),
            'status' => $this->flattenText($item['status'] ?? ($isManual ? 'verified' : 'raw')),
            'is_manual' => $isManual,
            'excerpt' => Str::limit($description, 260),
        ];
    }

    private function manualSections(array $item): array
    {
        $sections = [
            'trigger' => 'Срабатывание',
            'detection' => 'Обнаружение',
            'disarming' => 'Обезвреживание',
            'infection' => 'Заражение',
            'incubation' => 'Инкубация',
            'symptoms' => 'Симптомы',
            'treatment' => 'Лечение',
        ];

        return collect($sections)
            ->map(function (string $title, string $key) use ($item): ?array {
                $content = $this->normalizeTextList($item[$key] ?? []);

                if ($content === []) {
                    return null;
                }

                return [
                    'key' => $key,
                    'title' => $title,
                    'items' => $content,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeTextList(mixed $value): array
    {
        if (! is_array($value)) {
            $value = $value ? [$value] : [];
        }

        return collect($value)
            ->map(fn (mixed $part): string => $this->flattenText($part))
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

    private function readJson(string $relativePath, mixed $default): mixed
    {
        $path = $this->basePath().DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

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

    private function readManualJson(string $relativePath, mixed $default): mixed
    {
        $path = $this->manualBasePath().DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

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
        return 'dmg_data.'.md5($this->basePath().'|'.$this->manualBasePath()).'.'.$this->dataVersion().'.'.$suffix;
    }

    private function dataVersion(): int
    {
        $paths = array_filter([
            $this->basePath(),
            $this->manualBasePath(),
        ], 'is_dir');

        $latest = 0;

        foreach ($paths as $path) {
            $latest = max($latest, (int) filemtime($path));

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $latest = max($latest, (int) $file->getMTime());
                }
            }
        }

        return $latest;
    }
}
