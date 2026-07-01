<?php

namespace App\Http\Controllers;

use App\Models\Background;
use App\Models\Character;
use App\Models\CharacterClass;
use App\Models\CharacterSubclass;
use App\Models\Language;
use App\Models\Race;
use App\Models\RaceSubrace;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CharacterController extends Controller
{
    public function index(): View
    {
        $characters = auth()
            ->user()
            ->characters()
            ->with(['race', 'subrace', 'characterClass', 'characterSubclass', 'background'])
            ->latest()
            ->get();

        return view('characters.index', compact('characters'));
    }

    public function create(): View
    {
        return view('characters.create', $this->formDictionaries());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedCharacterData($request);
        $data['user_id'] = auth()->id();

        $character = Character::create($data);

        return redirect()
            ->route('characters.show', $character)
            ->with('success', __('ui.messages.character_created'));
    }

    public function show(Character $character): View
    {
        $this->authorizeCharacter($character);

        $character->load(['race', 'subrace', 'characterClass', 'characterSubclass', 'background']);

        $skillsBySlug = Skill::orderBy('name')->get()->keyBy('slug');
        $languagesBySlug = Language::orderBy('name')->get()->keyBy('slug');

        return view('characters.show', compact('character', 'skillsBySlug', 'languagesBySlug'));
    }

    public function edit(Character $character): View
    {
        $this->authorizeCharacter($character);

        return view('characters.edit', [
            'character' => $character,
            ...$this->formDictionaries(),
        ]);
    }

    public function update(Request $request, Character $character): RedirectResponse
    {
        $this->authorizeCharacter($character);

        $character->update($this->validatedCharacterData($request));

        return redirect()
            ->route('characters.show', $character)
            ->with('success', __('ui.messages.character_updated'));
    }

    public function updateVitals(Request $request, Character $character): JsonResponse|RedirectResponse
    {
        $this->authorizeCharacter($character);

        $data = $request->validate([
            'current_hp' => ['required', 'integer', 'min:0', 'max:100'],
            'experience' => ['required', 'integer', 'min:0'],
        ]);

        $currentHp = min((int) $data['current_hp'], max(0, (int) $character->max_hp));
        $experience = (int) $data['experience'];

        $character->update([
            'current_hp' => $currentHp,
            'experience' => $experience,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'current_hp' => $currentHp,
                'max_hp' => (int) $character->max_hp,
                'experience' => $experience,
            ]);
        }

        return redirect()
            ->route('characters.show', $character)
            ->with('success', __('ui.messages.vitals_updated'));
    }

    public function destroy(Character $character): RedirectResponse
    {
        $this->authorizeCharacter($character);

        $character->delete();

        return redirect()
            ->route('characters.index')
            ->with('success', __('ui.messages.character_deleted'));
    }

    /**
     * @return array{
     *     races: \Illuminate\Database\Eloquent\Collection<int, Race>,
     *     subraces: \Illuminate\Database\Eloquent\Collection<int, RaceSubrace>,
     *     classes: \Illuminate\Database\Eloquent\Collection<int, CharacterClass>,
     *     subclasses: \Illuminate\Database\Eloquent\Collection<int, CharacterSubclass>,
     *     backgrounds: \Illuminate\Database\Eloquent\Collection<int, Background>,
     *     skills: \Illuminate\Database\Eloquent\Collection<int, Skill>,
     *     languages: \Illuminate\Database\Eloquent\Collection<int, Language>
     * }
     */
    private function formDictionaries(): array
    {
        return [
            'races' => Race::orderBy('name')->get(),
            'subraces' => RaceSubrace::orderBy('name')->get(),
            'classes' => CharacterClass::orderBy('name')->get(),
            'subclasses' => CharacterSubclass::orderBy('name')->get(),
            'backgrounds' => Background::orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->get(),
            'languages' => Language::orderBy('name')->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedCharacterData(Request $request): array
    {
        $data = $request->validate([
            'race_id' => ['nullable', 'exists:races,id'],
            'subrace_id' => ['nullable', 'exists:race_subraces,id'],
            'class_id' => ['nullable', 'exists:character_classes,id'],
            'subclass_id' => ['nullable', 'exists:character_subclasses,id'],
            'background_id' => ['nullable', 'exists:backgrounds,id'],

            'name' => ['required', 'string', 'max:100'],
            'player_name' => ['nullable', 'string', 'max:100'],

            'level' => ['required', 'integer', 'min:1', 'max:30'],
            'experience' => ['nullable', 'integer', 'min:0'],
            'alignment' => ['nullable', 'string', 'max:100'],

            'strength' => ['required', 'integer', 'min:1', 'max:30'],
            'dexterity' => ['required', 'integer', 'min:1', 'max:30'],
            'constitution' => ['required', 'integer', 'min:1', 'max:30'],
            'intelligence' => ['required', 'integer', 'min:1', 'max:30'],
            'wisdom' => ['required', 'integer', 'min:1', 'max:30'],
            'charisma' => ['required', 'integer', 'min:1', 'max:30'],

            'max_hp' => ['nullable', 'integer', 'min:0', 'max:100'],
            'current_hp' => ['nullable', 'integer', 'min:0', 'max:100'],
            'armor_class' => ['nullable', 'integer', 'min:0', 'max:30'],
            'armor_class_mode' => ['nullable', 'in:auto,manual'],
            'speed' => ['nullable', 'integer', 'min:0', 'max:100'],

            'skill_proficiencies' => ['nullable', 'array'],
            'skill_proficiencies.*' => ['string', 'exists:skills,slug'],

            'language_proficiencies' => ['nullable', 'array'],
            'language_proficiencies.*' => ['string', 'exists:languages,slug'],

            'custom_armor_proficiencies_text' => ['nullable', 'string'],
            'custom_weapon_proficiencies_text' => ['nullable', 'string'],
            'custom_tool_proficiencies_text' => ['nullable', 'string'],
            'equipment_text' => ['nullable', 'string'],

            'personality_traits' => ['nullable', 'string'],
            'ideals' => ['nullable', 'string'],
            'bonds' => ['nullable', 'string'],
            'flaws' => ['nullable', 'string'],
            'backstory' => ['nullable', 'string'],
        ]);

        $data['experience'] ??= 0;
        $data['max_hp'] ??= 0;
        $data['current_hp'] ??= $data['max_hp'];
        $data['armor_class'] ??= 10;
        $data['speed'] ??= 30;
        $data['skill_proficiencies'] ??= [];
        $data['language_proficiencies'] ??= [];
        $data['custom_armor_proficiencies'] = $this->proficiencyLines($data['custom_armor_proficiencies_text'] ?? null);
        $data['custom_weapon_proficiencies'] = $this->proficiencyLines($data['custom_weapon_proficiencies_text'] ?? null);
        $data['custom_tool_proficiencies'] = $this->proficiencyLines($data['custom_tool_proficiencies_text'] ?? null);

        $race = isset($data['race_id']) ? Race::find($data['race_id']) : null;
        $subrace = isset($data['subrace_id']) ? RaceSubrace::find($data['subrace_id']) : null;
        $class = isset($data['class_id']) ? CharacterClass::find($data['class_id']) : null;
        $subclass = isset($data['subclass_id']) ? CharacterSubclass::find($data['subclass_id']) : null;
        $background = isset($data['background_id']) ? Background::find($data['background_id']) : null;

        if (! $race || ! $subrace || (int) $subrace->race_id !== (int) $race->id) {
            $data['subrace_id'] = null;
            $subrace = null;
        }

        if (! $class || ! $subclass || (int) $subclass->class_id !== (int) $class->id) {
            $data['subclass_id'] = null;
            $subclass = null;
        }

        if ($race) {
            $data['speed'] = min(100, max(0, (int) $race->speed));
        }

        if ($subrace && $subrace->speed !== null) {
            $data['speed'] = min(100, max(0, (int) $subrace->speed));
        }

        $constitutionScore = $this->totalAbilityScore((int) $data['constitution'], $this->abilityBonus($race, $subrace, 'constitution'));
        $dexterityScore = $this->totalAbilityScore((int) $data['dexterity'], $this->abilityBonus($race, $subrace, 'dexterity'));

        if ($class && (int) $data['max_hp'] === 0) {
            $data['max_hp'] = $this->maxHitPoints($class->hit_die, (int) $data['level'], $constitutionScore);
            $data['current_hp'] = $data['max_hp'];
        }

        $baseArmorClass = $this->baseArmorClass($dexterityScore);
        $armorClassMode = $data['armor_class_mode'] ?? 'auto';

        if ($armorClassMode === 'auto' || (int) $data['armor_class'] === 10) {
            $data['armor_class'] = min(30, max(0, $baseArmorClass));
        }

        if ((int) $data['max_hp'] > 0) {
            $data['current_hp'] = min((int) $data['current_hp'], (int) $data['max_hp']);
        }

        $data['skill_proficiencies'] = collect($data['skill_proficiencies'])
            ->merge($background?->skill_proficiencies ?? [])
            ->filter(fn (string $slug): bool => ! str_starts_with($slug, 'choose:'))
            ->unique()
            ->values()
            ->all();

        $data['language_proficiencies'] = collect($data['language_proficiencies'])
            ->merge($race?->languages ?? [])
            ->merge($subrace?->languages ?? [])
            ->merge($background?->languages ?? [])
            ->filter(fn (string $slug): bool => ! str_starts_with($slug, 'choose:'))
            ->unique()
            ->values()
            ->all();

        $data['features'] = collect()
            ->merge($race?->features ?? [])
            ->merge($subrace?->features ?? [])
            ->merge($class?->features ?? [])
            ->merge($this->levelFeatures($subclass?->features_by_level ?? [], (int) $data['level']))
            ->merge($background?->features ?? [])
            ->unique()
            ->values()
            ->all();

        $data['equipment'] = collect(preg_split('/\r\n|\r|\n/', $data['equipment_text'] ?? ''))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();

        unset($data['equipment_text']);
        unset($data['custom_armor_proficiencies_text']);
        unset($data['custom_weapon_proficiencies_text']);
        unset($data['custom_tool_proficiencies_text']);
        unset($data['armor_class_mode']);

        return $data;
    }

    private function authorizeCharacter(Character $character): void
    {
        abort_if($character->user_id !== auth()->id(), 403);
    }

    private function abilityModifier(int $score): int
    {
        return (int) floor(($score - 10) / 2);
    }

    private function totalAbilityScore(int $score, int $bonus): int
    {
        return min(30, max(1, $score + $bonus));
    }

    private function abilityBonus(?Race $race, ?RaceSubrace $subrace, string $ability): int
    {
        return (int) ($race?->ability_bonuses[$ability] ?? 0)
            + (int) ($subrace?->ability_bonuses[$ability] ?? 0);
    }

    private function baseArmorClass(int $dexterityScore): int
    {
        return max(1, 10 + $this->abilityModifier($dexterityScore));
    }

    private function maxHitPoints(int $hitDie, int $level, int $constitutionScore): int
    {
        $constitutionModifier = $this->abilityModifier($constitutionScore);
        $firstLevelHp = max(1, $hitDie + $constitutionModifier);
        $nextLevelHp = max(1, ((int) floor($hitDie / 2) + 1) + $constitutionModifier);

        return min(100, $firstLevelHp + max(0, $level - 1) * $nextLevelHp);
    }

    /**
     * @param  array<int|string, list<string>>  $featuresByLevel
     * @return list<string>
     */
    private function levelFeatures(array $featuresByLevel, int $level): array
    {
        return collect($featuresByLevel)
            ->filter(fn (array $features, int|string $requiredLevel): bool => (int) $requiredLevel <= $level)
            ->flatten()
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    private function proficiencyLines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $text ?? ''))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
