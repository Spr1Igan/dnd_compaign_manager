@php
    $selectedSkills = old('skill_proficiencies', $character?->skill_proficiencies ?? []);
    $selectedLanguages = old('language_proficiencies', $character?->language_proficiencies ?? []);
    $customArmorProficiencies = old('custom_armor_proficiencies_text', implode("\n", $character?->custom_armor_proficiencies ?? []));
    $customWeaponProficiencies = old('custom_weapon_proficiencies_text', implode("\n", $character?->custom_weapon_proficiencies ?? []));
    $customToolProficiencies = old('custom_tool_proficiencies_text', implode("\n", $character?->custom_tool_proficiencies ?? []));
    $equipment = old('equipment_text', implode("\n", $character?->equipment ?? []));
    $armorClassMode = old('armor_class_mode', $character && ! $character->usesBaseArmorClass() ? 'manual' : 'auto');
    $armorClassValue = old('armor_class', $character?->effectiveArmorClass() ?? 10);
    $ruleLabel = fn (?string $value): string => $value ? \App\Models\Character::readableRuleLabel($value) : '—';
    $sizeSlug = fn (?string $value): ?string => match ($value) {
        'Средний' => 'medium',
        'Маленький' => 'small',
        default => $value,
    };

    $abilities = collect(__('game.abilities'))
        ->map(fn (array $ability) => [$ability['name'], $ability['abbr']])
        ->all();

    $abilityNames = collect(__('game.abilities'))
        ->map(fn (array $ability) => $ability['name'])
        ->all();

    $ruleLabels = \App\Models\Character::ruleLabels();

    $abilityHelp = __('sheet.ability_help');
    $skillHelp = __('sheet.skill_help');

    $ruleData = [
        'races' => $races->mapWithKeys(fn ($race) => [
            $race->id => [
                'name' => $ruleLabel($race->slug),
                'speed' => $race->speed,
                'size' => $ruleLabel($sizeSlug($race->size)),
                'languages' => $race->languages ?? [],
                'features' => $race->features ?? [],
                'ability_bonuses' => $race->ability_bonuses ?? [],
            ],
        ]),
        'subraces' => $subraces->mapWithKeys(fn ($subrace) => [
            $subrace->id => [
                'id' => $subrace->id,
                'race_id' => $subrace->race_id,
                'name' => $ruleLabel($subrace->slug),
                'speed' => $subrace->speed,
                'languages' => $subrace->languages ?? [],
                'features' => $subrace->features ?? [],
                'ability_bonuses' => $subrace->ability_bonuses ?? [],
            ],
        ]),
        'classes' => $classes->mapWithKeys(fn ($class) => [
            $class->id => [
                'name' => $ruleLabel($class->slug),
                'hit_die' => $class->hit_die,
                'saving_throws' => $class->saving_throws ?? [],
                'armor_proficiencies' => $class->armor_proficiencies ?? [],
                'weapon_proficiencies' => $class->weapon_proficiencies ?? [],
                'tool_proficiencies' => $class->tool_proficiencies ?? [],
                'skill_choices' => $class->skill_choices ?? [],
                'features' => $class->features ?? [],
            ],
        ]),
        'subclasses' => $subclasses->mapWithKeys(fn ($subclass) => [
            $subclass->id => [
                'id' => $subclass->id,
                'class_id' => $subclass->class_id,
                'name' => $ruleLabel($subclass->slug),
                'features_by_level' => $subclass->features_by_level ?? [],
            ],
        ]),
        'backgrounds' => $backgrounds->mapWithKeys(fn ($background) => [
            $background->id => [
                'name' => $ruleLabel($background->slug),
                'skill_proficiencies' => $background->skill_proficiencies ?? [],
                'tool_proficiencies' => $background->tool_proficiencies ?? [],
                'languages' => $background->languages ?? [],
                'equipment' => $background->equipment ?? [],
                'features' => $background->features ?? [],
            ],
        ]),
        'abilityNames' => $abilityNames,
        'ruleLabels' => $ruleLabels,
        'featureHelp' => __('game.feature_help'),
        'text' => __('sheet.js'),
    ];
@endphp

<form class="character-sheet-form dnd-sheet-form" method="POST" action="{{ $action }}" data-character-sheet-form>
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="paper-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="sheet-banner">
        <div class="banner-field banner-class">
            <label for="class_id">{{ __('ui.form.class') }}</label>
            <select id="class_id" name="class_id">
                <option value="">{{ __('ui.form.no_class') }}</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected(old('class_id', $character?->class_id) == $class->id)>
                        {{ $ruleLabel($class->slug) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="banner-field">
            <label for="subclass_id">{{ __('ui.form.subclass') }}</label>
            <select id="subclass_id" name="subclass_id">
                <option value="">{{ __('ui.form.no_subclass') }}</option>
                @foreach ($subclasses as $subclass)
                    <option value="{{ $subclass->id }}" data-class-id="{{ $subclass->class_id }}" @selected(old('subclass_id', $character?->subclass_id) == $subclass->id)>
                        {{ $ruleLabel($subclass->slug) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="banner-field banner-level">
            <label for="level">{{ __('ui.form.level') }}</label>
            <input id="level" type="number" name="level" min="1" max="30" value="{{ old('level', $character?->level ?? 1) }}" required>
        </div>

        <div class="banner-name">
            <label for="name">{{ __('ui.form.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $character?->name) }}" required>
        </div>

        <div class="banner-field">
            <label for="background_id">{{ __('ui.form.background') }}</label>
            <select id="background_id" name="background_id">
                <option value="">{{ __('ui.form.no_background') }}</option>
                @foreach ($backgrounds as $background)
                    <option value="{{ $background->id }}" @selected(old('background_id', $character?->background_id) == $background->id)>
                        {{ $ruleLabel($background->slug) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="banner-field">
            <label for="alignment">{{ __('ui.form.alignment') }}</label>
            <input id="alignment" type="text" name="alignment" value="{{ old('alignment', $character?->alignment) }}">
        </div>
    </section>

    <section class="sheet-quick-row">
        <div>
            <label for="race_id">{{ __('ui.form.race') }}</label>
            <select id="race_id" name="race_id">
                <option value="">{{ __('ui.form.no_race') }}</option>
                @foreach ($races as $race)
                    <option value="{{ $race->id }}" @selected(old('race_id', $character?->race_id) == $race->id)>
                        {{ $ruleLabel($race->slug) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="subrace_id">{{ __('ui.form.subrace') }}</label>
            <select id="subrace_id" name="subrace_id">
                <option value="">{{ __('ui.form.no_subrace') }}</option>
                @foreach ($subraces as $subrace)
                    <option value="{{ $subrace->id }}" data-race-id="{{ $subrace->race_id }}" @selected(old('subrace_id', $character?->subrace_id) == $subrace->id)>
                        {{ $ruleLabel($subrace->slug) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="player_name">{{ __('ui.form.player') }}</label>
            <input id="player_name" type="text" name="player_name" value="{{ old('player_name', $character?->player_name) }}">
        </div>

        <div>
            <label for="experience">{{ __('ui.form.experience') }}</label>
            <input id="experience" type="number" name="experience" min="0" value="{{ old('experience', $character?->experience ?? 0) }}">
        </div>
    </section>

    <section class="sheet-abilities-strip">
        @foreach ($abilities as $field => [$label, $abbr])
            <div class="sheet-ability-card">
                <button
                    class="sheet-help-trigger"
                    type="button"
                    data-help-title="{{ $label }}"
                    data-help-body="{{ $abilityHelp[$field] }}"
                    aria-label="{{ __('sheet.labels.hint_for', ['name' => $label]) }}"
                >?</button>
                <label for="{{ $field }}">
                    <span>{{ $abbr }}</span>
                    {{ $label }}
                </label>
                <input id="{{ $field }}" type="number" name="{{ $field }}" min="1" max="30" value="{{ old($field, $character?->$field ?? 10) }}" required data-ability-input="{{ $field }}">
                <small class="ability-modifier" data-ability-summary="{{ $field }}">+0</small>
            </div>
        @endforeach
    </section>

    <section class="sheet-panel sheet-rules-panel">
        <h2>{{ __('ui.form.pulled_by_choice') }}</h2>
        <div class="rule-summary-grid">
            <p><b>{{ __('ui.form.race') }}:</b> <span data-rule-race>{{ __('ui.form.choose_race') }}</span></p>
            <p><b>{{ __('ui.form.subrace') }}:</b> <span data-rule-subrace>{{ __('ui.form.choose_subrace') }}</span></p>
            <p><b>{{ __('ui.form.class') }}:</b> <span data-rule-class>{{ __('ui.form.choose_class') }}</span></p>
            <p><b>{{ __('ui.form.subclass') }}:</b> <span data-rule-subclass>{{ __('ui.form.choose_subclass') }}</span></p>
            <p><b>{{ __('ui.form.background') }}:</b> <span data-rule-background>{{ __('ui.form.choose_background') }}</span></p>
            <p><b>{{ __('ui.form.armor_proficiencies') }}:</b> <span data-rule-armor>{{ __('ui.dash') }}</span></p>
            <p><b>{{ __('ui.form.weapon_proficiencies') }}:</b> <span data-rule-weapons>{{ __('ui.dash') }}</span></p>
            <p><b>{{ __('ui.form.tool_proficiencies') }}:</b> <span data-rule-tools>{{ __('ui.dash') }}</span></p>
            <p><b>{{ __('ui.form.features') }}:</b> <span class="feature-chip-list" data-rule-features>{{ __('ui.form.not_yet') }}</span></p>
        </div>
    </section>

    <section class="sheet-panel lined-panel custom-proficiencies-panel">
        <h2>{{ __('ui.form.additional_proficiencies') }}</h2>
        <p class="sheet-note">{{ __('ui.form.fixed_proficiencies_note') }}</p>

        <div class="custom-proficiency-grid">
            <label>
                <span>{{ __('ui.form.custom_armor_proficiencies') }}</span>
                <textarea name="custom_armor_proficiencies_text" rows="4" placeholder="{{ __('ui.form.custom_proficiency_placeholder') }}">{{ $customArmorProficiencies }}</textarea>
            </label>

            <label>
                <span>{{ __('ui.form.custom_weapon_proficiencies') }}</span>
                <textarea name="custom_weapon_proficiencies_text" rows="4" placeholder="{{ __('ui.form.custom_proficiency_placeholder') }}">{{ $customWeaponProficiencies }}</textarea>
            </label>

            <label>
                <span>{{ __('ui.form.custom_tool_proficiencies') }}</span>
                <textarea name="custom_tool_proficiencies_text" rows="4" placeholder="{{ __('ui.form.custom_proficiency_placeholder') }}">{{ $customToolProficiencies }}</textarea>
            </label>
        </div>
    </section>

    <div class="sheet-main-grid">
        <aside class="sheet-sidebar">
            <section class="sheet-panel compact">
                <h2>
                    {{ __('ui.form.saving_throws') }}
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="{{ __('ui.form.saving_throws') }}"
                        data-help-body="{{ __('sheet.help.saving_throws') }}"
                        aria-label="{{ __('sheet.labels.hint_for', ['name' => __('ui.form.saving_throws')]) }}"
                    >?</button>
                </h2>
                <div class="save-list">
                    @foreach ($abilityNames as $field => $label)
                        <label
                            data-save-ability="{{ $field }}"
                            data-help-title="{{ __('sheet.labels.saving_throw_for', ['name' => $label]) }}"
                            data-help-body="{{ __('sheet.help.saving_throw_item', ['ability' => $label]) }}"
                        >
                            <span class="sheet-dot"></span>
                            <input type="checkbox" disabled>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="sheet-panel compact">
                <h2>
                    {{ __('ui.form.skills') }}
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="{{ __('ui.form.skills') }}"
                        data-help-body="{{ __('sheet.help.skills_section') }}"
                        data-skill-section-help
                        aria-label="{{ __('sheet.labels.hint_for', ['name' => __('ui.form.skills')]) }}"
                    >?</button>
                </h2>
                <div class="skill-list">
                    @foreach ($skills as $skill)
                        <label>
                            <input
                                type="checkbox"
                                name="skill_proficiencies[]"
                                value="{{ $skill->slug }}"
                                @checked(in_array($skill->slug, $selectedSkills, true))
                                data-skill-input="{{ $skill->slug }}"
                            >
                            <span>{{ $ruleLabel($skill->slug) }}</span>
                            <small>{{ $abilityNames[$skill->ability] ?? $skill->ability }}</small>
                            <button
                                class="sheet-help-trigger skill-help"
                                type="button"
                                data-help-title="{{ $ruleLabel($skill->slug) }}"
                                data-help-body="{{ $skillHelp[$skill->slug] ?? __('sheet.help.skill_default') }}"
                                aria-label="{{ __('sheet.labels.hint_for', ['name' => $ruleLabel($skill->slug)]) }}"
                            >?</button>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="sheet-panel compact">
                <h2>{{ __('ui.form.languages') }}</h2>
                <div class="language-list">
                    @foreach ($languages as $language)
                        <label>
                            <input
                                type="checkbox"
                                name="language_proficiencies[]"
                                value="{{ $language->slug }}"
                                @checked(in_array($language->slug, $selectedLanguages, true))
                                data-language-input="{{ $language->slug }}"
                            >
                            <span>{{ $ruleLabel($language->slug) }}</span>
                            <small>{{ $ruleLabel($language->type) }}</small>
                        </label>
                    @endforeach
                </div>
            </section>
        </aside>

        <div class="sheet-core">
            <section class="sheet-panel combat-panel">
                <h2>{{ __('ui.form.combat') }}</h2>
                <div class="combat-stat-grid">
                    <div class="circle-stat">
                        <button
                            class="sheet-help-trigger stat-help"
                            type="button"
                            data-help-title="{{ __('ui.form.armor_class') }}"
                            data-help-body="{{ __('sheet.help.armor_class') }}"
                            aria-label="{{ __('sheet.labels.hint_for', ['name' => __('ui.form.armor_class')]) }}"
                        >?</button>
                        <label for="armor_class">{{ __('ui.form.armor_class') }}</label>
                        <input id="armor_class" type="number" name="armor_class" min="0" max="30" value="{{ $armorClassValue }}">
                        <input type="hidden" name="armor_class_mode" value="{{ $armorClassMode }}" data-armor-class-mode>
                        <label class="stat-auto-toggle">
                            <input type="checkbox" data-armor-auto-toggle @checked($armorClassMode === 'auto')>
                            {{ __('ui.form.auto') }}
                        </label>
                    </div>

                    <div class="circle-stat">
                        <label for="speed">{{ __('ui.form.speed') }}</label>
                        <input id="speed" type="number" name="speed" min="0" max="100" value="{{ old('speed', $character?->speed ?? 30) }}">
                    </div>

                    <div class="hp-box">
                        <button
                            class="sheet-help-trigger stat-help"
                            type="button"
                            data-help-title="{{ __('ui.form.max_hp') }}"
                            data-help-body="{{ __('ui.form.max_hp') }}"
                            data-max-hp-help
                            aria-label="{{ __('ui.form.max_hp') }}"
                        >?</button>
                        <label for="max_hp">{{ __('ui.form.max_hp') }}</label>
                        <input id="max_hp" type="number" name="max_hp" min="0" max="100" value="{{ old('max_hp', $character?->max_hp ?? 0) }}">
                    </div>

                    <div class="hp-box">
                        <label for="current_hp">{{ __('ui.form.current_hp') }}</label>
                        <input id="current_hp" type="number" name="current_hp" min="0" max="{{ min(100, old('max_hp', $character?->max_hp ?? 100) ?: 100) }}" value="{{ old('current_hp', $character?->current_hp ?? 0) }}">
                    </div>
                </div>
            </section>

            <section class="sheet-panel lined-panel">
                <h2>{{ __('ui.form.traits_and_abilities') }}</h2>
                <textarea name="personality_traits" rows="4" placeholder="{{ __('ui.form.personality_traits') }}">{{ old('personality_traits', $character?->personality_traits) }}</textarea>
                <textarea name="ideals" rows="3" placeholder="{{ __('ui.form.ideals') }}">{{ old('ideals', $character?->ideals) }}</textarea>
                <textarea name="bonds" rows="3" placeholder="{{ __('ui.form.bonds') }}">{{ old('bonds', $character?->bonds) }}</textarea>
                <textarea name="flaws" rows="3" placeholder="{{ __('ui.form.flaws') }}">{{ old('flaws', $character?->flaws) }}</textarea>
            </section>

            <section class="sheet-panel lined-panel">
                <h2>{{ __('ui.form.equipment') }}</h2>
                <textarea name="equipment_text" rows="8" placeholder="{{ __('ui.form.equipment_placeholder') }}">{{ $equipment }}</textarea>
            </section>

            <section class="sheet-panel lined-panel">
                <h2>{{ __('ui.form.backstory') }}</h2>
                <textarea name="backstory" rows="10">{{ old('backstory', $character?->backstory) }}</textarea>
            </section>
        </div>
    </div>

    <div class="form-actions sheet-actions">
        <button class="paper-button" type="submit">
            {{ __('ui.save_sheet') }}
        </button>

        <a class="paper-button secondary" href="{{ route('characters.index') }}">
            {{ __('ui.back') }}
        </a>
    </div>
</form>

<div class="sheet-help-overlay" data-sheet-help-overlay hidden>
    <section class="sheet-help-popover" role="dialog" aria-modal="true" aria-labelledby="sheet-help-title">
        <button class="sheet-help-close" type="button" data-sheet-help-close aria-label="{{ __('ui.form.close_hint') }}">×</button>
        <p class="eyebrow">{{ __('ui.form.sheet_hint') }}</p>
        <h2 id="sheet-help-title" data-sheet-help-title></h2>
        <p data-sheet-help-body></p>
    </section>
</div>

@push('scripts')
    <script>
        window.characterSheetRules = @json($ruleData);
    </script>
    <script>
        (() => {
            const form = document.querySelector('[data-character-sheet-form]');

            if (!form || !window.characterSheetRules) {
                return;
            }

            const rules = window.characterSheetRules;
            const text = rules.text || {};
            const formatText = (template, replacements = {}) => Object.entries(replacements).reduce(
                (value, [key, replacement]) => value.replaceAll(`:${key}`, String(replacement)),
                String(template || ''),
            );
            const raceSelect = form.querySelector('#race_id');
            const subraceSelect = form.querySelector('#subrace_id');
            const classSelect = form.querySelector('#class_id');
            const subclassSelect = form.querySelector('#subclass_id');
            const backgroundSelect = form.querySelector('#background_id');
            const levelInput = form.querySelector('#level');
            const constitutionInput = form.querySelector('#constitution');
            const speedInput = form.querySelector('#speed');
            const maxHpInput = form.querySelector('#max_hp');
            const currentHpInput = form.querySelector('#current_hp');
            const armorClassInput = form.querySelector('#armor_class');
            const armorClassModeInput = form.querySelector('[data-armor-class-mode]');
            const armorAutoToggle = form.querySelector('[data-armor-auto-toggle]');
            const equipmentText = form.querySelector('[name="equipment_text"]');
            const skillSectionHelp = form.querySelector('[data-skill-section-help]');
            const maxHpHelp = form.querySelector('[data-max-hp-help]');
            let armorClassIsManual = armorClassModeInput?.value === 'manual';

            const labelize = (item) => String(item).replaceAll('-', ' ');
            const translate = (item) => String(item).startsWith('choose:') ? (text.choose || 'choose') : rules.abilityNames[item] ?? rules.ruleLabels[item] ?? labelize(item);
            const translateInContext = (item, context) => rules.ruleLabels[`${context}:${item}`] ?? translate(item);
            const plural = (count, one, few, many) => {
                const abs = Math.abs(Number(count));
                const mod10 = abs % 10;
                const mod100 = abs % 100;

                if (mod10 === 1 && mod100 !== 11) {
                    return one;
                }

                if (mod10 >= 2 && mod10 <= 4 && (mod100 < 12 || mod100 > 14)) {
                    return few;
                }

                return many;
            };

            const readable = (items) => {
                if (!items || items.length === 0) {
                    return text.none || 'none';
                }

                return items
                    .map(translate)
                    .join(', ') || (text.choose || 'choose');
            };
            const readableInContext = (items, context) => {
                if (!items || items.length === 0) {
                    return text.none || 'none';
                }

                return items
                    .map((item) => translateInContext(item, context))
                    .join(', ') || (text.choose || 'choose');
            };
            const featureHelpBody = (feature) => rules.featureHelp?.[feature]
                || formatText(text.feature_help_fallback, { feature: translate(feature) });
            const renderFeatureButtons = (target, items) => {
                if (!target) {
                    return;
                }

                const features = [...new Set(items ?? [])];

                if (features.length === 0) {
                    target.textContent = text.none || 'none';
                    return;
                }

                const fragment = document.createDocumentFragment();

                features.forEach((feature) => {
                    const button = document.createElement('button');
                    button.className = 'feature-chip';
                    button.type = 'button';
                    button.dataset.helpTitle = translate(feature);
                    button.dataset.helpBody = featureHelpBody(feature);
                    button.textContent = translate(feature);
                    fragment.append(button);
                });

                target.replaceChildren(fragment);
            };
            const mergedAbilityBonuses = (race, subrace) => {
                const bonuses = { ...(race?.ability_bonuses ?? {}) };

                Object.entries(subrace?.ability_bonuses ?? {}).forEach(([ability, bonus]) => {
                    bonuses[ability] = Number(bonuses[ability] ?? 0) + Number(bonus ?? 0);
                });

                return bonuses;
            };
            const activeSubrace = () => rules.subraces[subraceSelect?.value];
            const activeSubclass = () => rules.subclasses[subclassSelect?.value];
            const levelFeatures = (featuresByLevel) => {
                const level = clampNumber(levelInput?.value || 1, 1, 30);

                return Object.entries(featuresByLevel ?? {})
                    .filter(([requiredLevel]) => Number(requiredLevel) <= level)
                    .flatMap(([, features]) => features);
            };
            const syncDependentSelect = (select, parentValue, parentKey) => {
                if (!select) {
                    return;
                }

                let selectedAllowed = false;

                Array.from(select.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        option.disabled = false;
                        return;
                    }

                    const allowed = option.dataset[parentKey] === parentValue;
                    option.hidden = !allowed;
                    option.disabled = !allowed;

                    if (option.selected && allowed) {
                        selectedAllowed = true;
                    }
                });

                if (!selectedAllowed) {
                    select.value = '';
                }
            };

            const modifier = (score) => Math.floor((Number(score || 10) - 10) / 2);
            const signed = (value) => value >= 0 ? `+${value}` : String(value);
            const clampNumber = (value, min, max) => {
                const number = Number(value);

                if (!Number.isFinite(number)) {
                    return min;
                }

                return Math.min(max, Math.max(min, number));
            };
            const clampInput = (input, min, max) => {
                if (!input || input.value === '') {
                    return;
                }

                const nextValue = clampNumber(input.value, min, max);

                if (Number(input.value) !== nextValue) {
                    input.value = nextValue;
                }
            };
            const currentHpLimit = () => {
                if (!maxHpInput || maxHpInput.value === '') {
                    return 100;
                }

                return clampNumber(maxHpInput.value, 0, 100);
            };
            const clampCharacterNumbers = () => {
                clampInput(levelInput, 1, 30);
                clampInput(speedInput, 0, 100);
                clampInput(maxHpInput, 0, 100);
                clampInput(armorClassInput, 0, 30);

                form.querySelectorAll('[data-ability-input]').forEach((input) => {
                    clampInput(input, 1, 30);
                });

                if (currentHpInput) {
                    const maxHp = currentHpLimit();

                    currentHpInput.max = String(maxHp);
                    clampInput(currentHpInput, 0, maxHp);
                }
            };
            const clampAbility = (value) => Math.min(30, Math.max(1, Number(value || 10)));
            const baseArmorClass = () => Math.max(1, 10 + modifier(totalAbility('dexterity')));
            const totalAbility = (field) => {
                const race = rules.races[raceSelect.value];
                const subrace = activeSubrace();
                const input = form.querySelector(`[data-ability-input="${field}"]`);
                const bonuses = mergedAbilityBonuses(race, subrace);
                const bonus = Number(bonuses[field] ?? 0);

                return clampAbility(Number(input?.value || 10) + bonus);
            };

            const applyChecked = (selector, values) => {
                values
                    .filter((value) => !String(value).startsWith('choose:'))
                    .forEach((value) => {
                        const input = form.querySelector(`${selector}="${value}"]`);
                        if (input) {
                            input.checked = true;
                        }
                    });
            };

            const maxHpParts = (characterClass) => {
                const level = clampNumber(levelInput?.value || 1, 1, 30);
                const hitDie = Number(characterClass?.hit_die || 0);
                const constitutionModifier = modifier(totalAbility('constitution'));
                const averageHitDie = Math.floor(hitDie / 2) + 1;
                const firstLevelHp = Math.max(1, hitDie + constitutionModifier);
                const nextLevelHp = Math.max(1, averageHitDie + constitutionModifier);
                const expectedHp = Math.min(100, firstLevelHp + Math.max(0, level - 1) * nextLevelHp);

                return {
                    level,
                    hitDie,
                    constitutionModifier,
                    averageHitDie,
                    firstLevelHp,
                    nextLevelHp,
                    expectedHp,
                };
            };

            const classSkillChoiceSummary = (characterClass) => {
                const classChoices = characterClass?.skill_choices;

                if (!classChoices) {
                    return text.no_skill_choice_data || 'no skill choice data';
                }

                const count = Number(classChoices.choose || 0);
                const skillWords = text.skill_words || ['skill', 'skills', 'skills'];
                const countText = `${count} ${plural(count, skillWords[0], skillWords[1], skillWords[2])}`;

                return classChoices.from === 'any'
                    ? formatText(text.any_skills, { countText })
                    : formatText(text.from_list, { countText, list: readable(classChoices.from ?? []) });
            };

            const updateSkillHelp = (characterClass, background) => {
                if (!skillSectionHelp) {
                    return;
                }

                const base = text.skill_help_base;
                const classText = characterClass
                    ? formatText(text.class_skills, {
                        class: characterClass.name,
                        summary: classSkillChoiceSummary(characterClass),
                    })
                    : text.class_not_selected;
                const backgroundSkills = background?.skill_proficiencies ?? [];
                const backgroundText = background
                    ? formatText(text.background_skills, {
                        background: background.name,
                        skills: readable(backgroundSkills),
                    })
                    : text.background_not_selected;

                skillSectionHelp.dataset.helpBody = `${base} ${classText} ${backgroundText}`;
            };

            const updateMaxHpHelp = (characterClass) => {
                if (!maxHpHelp) {
                    return;
                }

                if (!characterClass) {
                    maxHpHelp.dataset.helpBody = text.max_hp_without_class;
                    return;
                }

                const {
                    level,
                    hitDie,
                    constitutionModifier,
                    averageHitDie,
                    firstLevelHp,
                    nextLevelHp,
                    expectedHp,
                } = maxHpParts(characterClass);
                const cappedText = expectedHp >= 100 ? text.max_hp_capped : '';

                maxHpHelp.dataset.helpBody = formatText(text.max_hp_formula, {
                    class: characterClass.name,
                    hitDie,
                    constitutionModifier: signed(constitutionModifier),
                    firstLevelHp,
                    averageHitDie,
                    nextLevelHp,
                    level,
                    expectedHp,
                    cappedText,
                });
            };

            const updateAbilities = () => {
                const race = rules.races[raceSelect.value];
                const subrace = activeSubrace();
                const bonuses = mergedAbilityBonuses(race, subrace);

                form.querySelectorAll('[data-ability-input]').forEach((input) => {
                    const field = input.dataset.abilityInput;
                    const bonus = Number(bonuses[field] ?? 0);
                    const total = totalAbility(field);
                    const target = form.querySelector(`[data-ability-summary="${field}"]`);

                    if (target) {
                        target.textContent = signed(modifier(total));
                        const bonusText = bonus
                            ? formatText(text.race_bonus_title, { bonus: signed(bonus) })
                            : '';

                        target.title = formatText(text.ability_title, {
                            bonusText,
                            total,
                            modifier: signed(modifier(total)),
                        });
                    }
                });
            };

            const updateRules = () => {
                clampCharacterNumbers();

                syncDependentSelect(subraceSelect, raceSelect.value, 'raceId');
                syncDependentSelect(subclassSelect, classSelect.value, 'classId');

                const race = rules.races[raceSelect.value];
                const subrace = activeSubrace();
                const characterClass = rules.classes[classSelect.value];
                const subclass = activeSubclass();
                const background = rules.backgrounds[backgroundSelect.value];

                if (race) {
                    speedInput.value = Math.min(100, Number(race.speed || 0));
                }

                if (subrace?.speed) {
                    speedInput.value = Math.min(100, Number(subrace.speed || 0));
                }

                if (characterClass && Number(maxHpInput.value || 0) === 0) {
                    const hp = maxHpParts(characterClass).expectedHp;
                    maxHpInput.value = hp;
                    currentHpInput.value = hp;
                }

                if (armorClassInput && !armorClassIsManual) {
                    armorClassInput.value = Math.min(30, baseArmorClass());
                }

                if (race) {
                    applyChecked('[data-language-input', race.languages ?? []);
                }

                if (subrace) {
                    applyChecked('[data-language-input', subrace.languages ?? []);
                }

                if (background) {
                    applyChecked('[data-skill-input', background.skill_proficiencies ?? []);
                    applyChecked('[data-language-input', background.languages ?? []);

                    if (equipmentText && equipmentText.value.trim() === '' && background.equipment?.length) {
                        equipmentText.value = background.equipment.join("\n");
                    }
                }

                form.querySelectorAll('[data-save-ability]').forEach((label) => {
                    const active = characterClass?.saving_throws?.includes(label.dataset.saveAbility);
                    label.classList.toggle('is-proficient', Boolean(active));
                });

                const features = [
                    ...(race?.features ?? []),
                    ...(subrace?.features ?? []),
                    ...(characterClass?.features ?? []),
                    ...levelFeatures(subclass?.features_by_level),
                    ...(background?.features ?? []),
                ];

                form.querySelector('[data-rule-race]').textContent = race
                    ? formatText(text.race_summary, {
                        name: race.name,
                        speed: race.speed,
                        size: race.size ?? '—',
                        languages: readable(race.languages),
                    })
                    : text.choose_race;
                form.querySelector('[data-rule-subrace]').textContent = subrace
                    ? formatText(text.subrace_summary, {
                        name: subrace.name,
                        speed: subrace.speed || race?.speed || '—',
                        languages: readable(subrace.languages),
                        features: readable(subrace.features),
                    })
                    : text.choose_subrace;
                form.querySelector('[data-rule-class]').textContent = characterClass
                    ? formatText(text.class_summary, {
                        name: characterClass.name,
                        hitDie: characterClass.hit_die,
                        savingThrows: readable(characterClass.saving_throws),
                        skills: classSkillChoiceSummary(characterClass),
                    })
                    : text.choose_class;
                form.querySelector('[data-rule-subclass]').textContent = subclass
                    ? formatText(text.subclass_summary, {
                        name: subclass.name,
                        features: readable(levelFeatures(subclass.features_by_level)),
                    })
                    : text.choose_subclass;
                form.querySelector('[data-rule-background]').textContent = background
                    ? formatText(text.background_summary, {
                        name: background.name,
                        skills: readable(background.skill_proficiencies),
                        languages: readable(background.languages),
                        tools: readable(background.tool_proficiencies),
                    })
                    : text.choose_background;
                form.querySelector('[data-rule-armor]').textContent = readableInContext(characterClass?.armor_proficiencies ?? [], 'armor');
                form.querySelector('[data-rule-weapons]').textContent = readable(characterClass?.weapon_proficiencies ?? []);
                form.querySelector('[data-rule-tools]').textContent = readable([
                    ...(characterClass?.tool_proficiencies ?? []),
                    ...(background?.tool_proficiencies ?? []),
                ].filter((value, index, values) => values.indexOf(value) === index));
                renderFeatureButtons(form.querySelector('[data-rule-features]'), features);

                updateSkillHelp(characterClass, background);
                updateMaxHpHelp(characterClass);
                clampCharacterNumbers();
                updateAbilities();
            };

            form.querySelectorAll('input[type="number"]').forEach((input) => {
                input.addEventListener('input', clampCharacterNumbers);
                input.addEventListener('change', clampCharacterNumbers);
            });

            form.addEventListener('submit', clampCharacterNumbers);

            [raceSelect, subraceSelect, classSelect, subclassSelect, backgroundSelect, levelInput, constitutionInput].forEach((element) => {
                element?.addEventListener('change', updateRules);
                element?.addEventListener('input', updateRules);
            });

            form.querySelectorAll('[data-ability-input]').forEach((input) => {
                input.addEventListener('input', updateRules);
            });

            armorClassInput?.addEventListener('input', () => {
                armorClassIsManual = true;
                armorClassModeInput.value = 'manual';
                armorAutoToggle.checked = false;
            });

            armorAutoToggle?.addEventListener('change', () => {
                armorClassIsManual = !armorAutoToggle.checked;
                armorClassModeInput.value = armorClassIsManual ? 'manual' : 'auto';

                if (!armorClassIsManual && armorClassInput) {
                    armorClassInput.value = baseArmorClass();
                }
            });

            updateRules();

            const helpOverlay = document.querySelector('[data-sheet-help-overlay]');
            const helpTitle = helpOverlay?.querySelector('[data-sheet-help-title]');
            const helpBody = helpOverlay?.querySelector('[data-sheet-help-body]');
            const helpClose = helpOverlay?.querySelector('[data-sheet-help-close]');

            const closeHelp = () => {
                helpOverlay?.setAttribute('hidden', '');
            };

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-help-title][data-help-body]');

                if (!trigger) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                helpTitle.textContent = trigger.dataset.helpTitle;
                helpBody.textContent = trigger.dataset.helpBody;
                helpOverlay.removeAttribute('hidden');
                helpClose.focus();
            });

            helpClose?.addEventListener('click', closeHelp);

            helpOverlay?.addEventListener('click', (event) => {
                if (event.target === helpOverlay) {
                    closeHelp();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !helpOverlay?.hasAttribute('hidden')) {
                    closeHelp();
                }
            });
        })();
    </script>
@endpush
