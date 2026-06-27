@php
    $selectedSkills = old('skill_proficiencies', $character?->skill_proficiencies ?? []);
    $selectedLanguages = old('language_proficiencies', $character?->language_proficiencies ?? []);
    $equipment = old('equipment_text', implode("\n", $character?->equipment ?? []));
    $armorClassMode = old('armor_class_mode', $character && ! $character->usesBaseArmorClass() ? 'manual' : 'auto');
    $armorClassValue = old('armor_class', $character?->effectiveArmorClass() ?? 10);

    $abilities = [
        'strength' => ['Сила', 'СИЛ'],
        'dexterity' => ['Ловкость', 'ЛОВ'],
        'constitution' => ['Телосложение', 'ТЕЛ'],
        'intelligence' => ['Интеллект', 'ИНТ'],
        'wisdom' => ['Мудрость', 'МДР'],
        'charisma' => ['Харизма', 'ХАР'],
    ];

    $abilityNames = [
        'strength' => 'Сила',
        'dexterity' => 'Ловкость',
        'constitution' => 'Телосложение',
        'intelligence' => 'Интеллект',
        'wisdom' => 'Мудрость',
        'charisma' => 'Харизма',
    ];

    $ruleLabels = [
        ...\App\Models\Character::ruleLabels(),
        'choose:1' => 'один на выбор',
        'choose:2' => 'два на выбор',
    ];

    $abilityHelp = [
        'strength' => 'Сила показывает физическую мощь. Используется для атак и урона оружием ближнего боя, Атлетики, переноски тяжестей, прыжков, лазания, толкания и силовых спасбросков.',
        'dexterity' => 'Ловкость отражает реакцию, равновесие и точность. Влияет на инициативу, КД без тяжёлых доспехов, атаки дальнобойным и фехтовальным оружием, Акробатику, Скрытность и ловкостные спасброски.',
        'constitution' => 'Телосложение отвечает за здоровье и выносливость. Модификатор добавляется к хитам за уровень и используется в спасбросках против яда, болезней, истощения и поддержания концентрации.',
        'intelligence' => 'Интеллект описывает память, обучение и логику. Используется для Магии, Истории, Анализа, Природы, Религии и часто важен для заклинаний волшебника.',
        'wisdom' => 'Мудрость отражает внимательность, интуицию и силу воли. Используется для Внимательности, Проницательности, Выживания, Медицины, ухода за животными и мудростных спасбросков.',
        'charisma' => 'Харизма показывает силу личности и влияние. Используется для Обмана, Запугивания, Выступления, Убеждения и часто важна для заклинаний барда, колдуна, паладина и чародея.',
    ];

    $skillHelp = [
        'athletics' => 'Атлетика зависит от Силы. Используется при лазании, прыжках, плавании, борьбе, толкании и других силовых действиях.',
        'acrobatics' => 'Акробатика зависит от Ловкости. Используется для равновесия, перекатов, ухода от падения и манёвров, где важна гибкость.',
        'sleight-of-hand' => 'Ловкость рук зависит от Ловкости. Используется для карманных краж, фокусов, скрытого манипулирования предметами.',
        'stealth' => 'Скрытность зависит от Ловкости. Используется, когда персонаж прячется, двигается тихо или пытается остаться незамеченным.',
        'arcana' => 'Магия зависит от Интеллекта. Используется для знаний о заклинаниях, магических предметах, планах существования и мистических символах.',
        'history' => 'История зависит от Интеллекта. Используется для знаний о прошлом, войнах, королевствах, легендах и важных событиях.',
        'investigation' => 'Анализ зависит от Интеллекта. Используется для поиска выводов по уликам, исследования механизмов, ловушек и скрытых деталей.',
        'nature' => 'Природа зависит от Интеллекта. Используется для знаний о местности, растениях, животных, погоде и природных циклах.',
        'religion' => 'Религия зависит от Интеллекта. Используется для знаний о богах, обрядах, святых символах, культах и планах, связанных с верой.',
        'animal-handling' => 'Уход за животными зависит от Мудрости. Используется для успокоения, контроля, понимания и обучения животных.',
        'insight' => 'Проницательность зависит от Мудрости. Используется, чтобы понять намерения, ложь, настроение и поведение существ.',
        'medicine' => 'Медицина зависит от Мудрости. Используется для стабилизации умирающих, диагностики болезней и ухода за ранами.',
        'perception' => 'Внимательность зависит от Мудрости. Используется для обнаружения скрытого, засад, звуков, запахов и важных деталей окружения.',
        'survival' => 'Выживание зависит от Мудрости. Используется для следопытства, охоты, поиска пути, предсказания погоды и жизни в дикой местности.',
        'deception' => 'Обман зависит от Харизмы. Используется для лжи, маскировки намерений, введения в заблуждение и игры роли.',
        'intimidation' => 'Запугивание зависит от Харизмы. Используется, чтобы давить угрозами, силой личности или демонстрацией опасности.',
        'performance' => 'Выступление зависит от Харизмы. Используется для музыки, актёрства, танца, рассказов и публичного развлечения.',
        'persuasion' => 'Убеждение зависит от Харизмы. Используется для переговоров, просьб, дипломатии, вдохновения и честного влияния.',
    ];

    $ruleData = [
        'races' => $races->mapWithKeys(fn ($race) => [
            $race->id => [
                'name' => $race->name,
                'speed' => $race->speed,
                'size' => $race->size,
                'languages' => $race->languages ?? [],
                'features' => $race->features ?? [],
                'ability_bonuses' => $race->ability_bonuses ?? [],
            ],
        ]),
        'classes' => $classes->mapWithKeys(fn ($class) => [
            $class->id => [
                'name' => $class->name,
                'hit_die' => $class->hit_die,
                'saving_throws' => $class->saving_throws ?? [],
                'armor_proficiencies' => $class->armor_proficiencies ?? [],
                'weapon_proficiencies' => $class->weapon_proficiencies ?? [],
                'tool_proficiencies' => $class->tool_proficiencies ?? [],
                'skill_choices' => $class->skill_choices ?? [],
                'features' => $class->features ?? [],
            ],
        ]),
        'backgrounds' => $backgrounds->mapWithKeys(fn ($background) => [
            $background->id => [
                'name' => $background->name,
                'skill_proficiencies' => $background->skill_proficiencies ?? [],
                'tool_proficiencies' => $background->tool_proficiencies ?? [],
                'languages' => $background->languages ?? [],
                'equipment' => $background->equipment ?? [],
                'features' => $background->features ?? [],
            ],
        ]),
        'abilityNames' => $abilityNames,
        'ruleLabels' => $ruleLabels,
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
            <label for="class_id">Класс</label>
            <select id="class_id" name="class_id">
                <option value="">Без класса</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected(old('class_id', $character?->class_id) == $class->id)>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="banner-field banner-level">
            <label for="level">Уровень</label>
            <input id="level" type="number" name="level" min="1" max="30" value="{{ old('level', $character?->level ?? 1) }}" required>
        </div>

        <div class="banner-name">
            <label for="name">Имя персонажа</label>
            <input id="name" type="text" name="name" value="{{ old('name', $character?->name) }}" required>
        </div>

        <div class="banner-field">
            <label for="background_id">Предыстория</label>
            <select id="background_id" name="background_id">
                <option value="">Без предыстории</option>
                @foreach ($backgrounds as $background)
                    <option value="{{ $background->id }}" @selected(old('background_id', $character?->background_id) == $background->id)>
                        {{ $background->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="banner-field">
            <label for="alignment">Мировоззрение</label>
            <input id="alignment" type="text" name="alignment" value="{{ old('alignment', $character?->alignment) }}">
        </div>
    </section>

    <section class="sheet-quick-row">
        <div>
            <label for="race_id">Раса</label>
            <select id="race_id" name="race_id">
                <option value="">Без расы</option>
                @foreach ($races as $race)
                    <option value="{{ $race->id }}" @selected(old('race_id', $character?->race_id) == $race->id)>
                        {{ $race->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="player_name">Игрок</label>
            <input id="player_name" type="text" name="player_name" value="{{ old('player_name', $character?->player_name) }}">
        </div>

        <div>
            <label for="experience">Опыт</label>
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
                    aria-label="Подсказка: {{ $label }}"
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
        <h2>Подтягивается по выбору</h2>
        <div class="rule-summary-grid">
            <p><b>Раса:</b> <span data-rule-race>выбери расу</span></p>
            <p><b>Класс:</b> <span data-rule-class>выбери класс</span></p>
            <p><b>Предыстория:</b> <span data-rule-background>выбери предысторию</span></p>
            <p><b>Особенности:</b> <span data-rule-features>пока нет</span></p>
        </div>
    </section>

    <div class="sheet-main-grid">
        <aside class="sheet-sidebar">
            <section class="sheet-panel compact">
                <h2>
                    Спасброски
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="Спасброски"
                        data-help-body="Спасбросок используется, когда персонаж пытается избежать опасности: яда, заклинания, ловушки, страха, падения или другого эффекта. Бросается d20, добавляется модификатор нужной характеристики, а если класс владеет этим спасброском — ещё бонус мастерства. Чем выше итог, тем больше шанс избежать или ослабить последствия."
                        aria-label="Подсказка: спасброски"
                    >?</button>
                </h2>
                <div class="save-list">
                    @foreach ($abilityNames as $field => $label)
                        <label
                            data-save-ability="{{ $field }}"
                            data-help-title="Спасбросок: {{ $label }}"
                            data-help-body="Этот спасбросок применяется, когда опасность проверяет характеристику «{{ $label }}». Если выбранный класс владеет этим спасброском, к броску добавляется бонус мастерства."
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
                    Навыки
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="Навыки"
                        data-help-body="Навык показывает, в каких действиях персонаж обучен. Когда мастер просит проверку навыка, бросается d20, добавляется модификатор связанной характеристики, а при владении навыком — бонус мастерства. Навыки помогают решать сцены исследования, общения, скрытности, знаний и выживания."
                        aria-label="Подсказка: навыки"
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
                            <span>{{ $skill->name }}</span>
                            <small>{{ $abilityNames[$skill->ability] ?? $skill->ability }}</small>
                            <button
                                class="sheet-help-trigger skill-help"
                                type="button"
                                data-help-title="{{ $skill->name }}"
                                data-help-body="{{ $skillHelp[$skill->slug] ?? 'Навык применяется по решению мастера, когда действие персонажа требует проверки.' }}"
                                aria-label="Подсказка: {{ $skill->name }}"
                            >?</button>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="sheet-panel compact">
                <h2>Языки</h2>
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
                            <span>{{ $language->name }}</span>
                            <small>{{ $language->type }}</small>
                        </label>
                    @endforeach
                </div>
            </section>
        </aside>

        <div class="sheet-core">
            <section class="sheet-panel combat-panel">
                <h2>Бой</h2>
                <div class="combat-stat-grid">
                    <div class="circle-stat">
                        <button
                            class="sheet-help-trigger stat-help"
                            type="button"
                            data-help-title="Класс Доспеха"
                            data-help-body="КД показывает, насколько трудно попасть по персонажу атакой. Без доспеха и щита базовый КД равен 10 + модификатор Ловкости. Доспехи, щит и некоторые способности могут менять формулу. Если атака врага равна КД или выше, она обычно попадает."
                            aria-label="Подсказка: класс доспеха"
                        >?</button>
                        <label for="armor_class">КД</label>
                        <input id="armor_class" type="number" name="armor_class" min="0" max="30" value="{{ $armorClassValue }}">
                        <input type="hidden" name="armor_class_mode" value="{{ $armorClassMode }}" data-armor-class-mode>
                        <label class="stat-auto-toggle">
                            <input type="checkbox" data-armor-auto-toggle @checked($armorClassMode === 'auto')>
                            авто
                        </label>
                    </div>

                    <div class="circle-stat">
                        <label for="speed">Скорость</label>
                        <input id="speed" type="number" name="speed" min="0" max="100" value="{{ old('speed', $character?->speed ?? 30) }}">
                    </div>

                    <div class="hp-box">
                        <label for="max_hp">Максимум HP</label>
                        <input id="max_hp" type="number" name="max_hp" min="0" max="100" value="{{ old('max_hp', $character?->max_hp ?? 0) }}">
                    </div>

                    <div class="hp-box">
                        <label for="current_hp">Текущие HP</label>
                        <input id="current_hp" type="number" name="current_hp" min="0" max="{{ min(100, old('max_hp', $character?->max_hp ?? 100) ?: 100) }}" value="{{ old('current_hp', $character?->current_hp ?? 0) }}">
                    </div>
                </div>
            </section>

            <section class="sheet-panel lined-panel">
                <h2>Черты и способности</h2>
                <textarea name="personality_traits" rows="4" placeholder="Черты характера">{{ old('personality_traits', $character?->personality_traits) }}</textarea>
                <textarea name="ideals" rows="3" placeholder="Идеалы">{{ old('ideals', $character?->ideals) }}</textarea>
                <textarea name="bonds" rows="3" placeholder="Привязанности">{{ old('bonds', $character?->bonds) }}</textarea>
                <textarea name="flaws" rows="3" placeholder="Слабости">{{ old('flaws', $character?->flaws) }}</textarea>
            </section>

            <section class="sheet-panel lined-panel">
                <h2>Снаряжение</h2>
                <textarea name="equipment_text" rows="8" placeholder="Каждый предмет с новой строки">{{ $equipment }}</textarea>
            </section>

            <section class="sheet-panel lined-panel">
                <h2>История</h2>
                <textarea name="backstory" rows="10">{{ old('backstory', $character?->backstory) }}</textarea>
            </section>
        </div>
    </div>

    <div class="form-actions sheet-actions">
        <button class="paper-button" type="submit">
            Сохранить лист
        </button>

        <a class="paper-button secondary" href="{{ route('characters.index') }}">
            Назад
        </a>
    </div>
</form>

<div class="sheet-help-overlay" data-sheet-help-overlay hidden>
    <section class="sheet-help-popover" role="dialog" aria-modal="true" aria-labelledby="sheet-help-title">
        <button class="sheet-help-close" type="button" data-sheet-help-close aria-label="Закрыть подсказку">×</button>
        <p class="eyebrow">Подсказка листа</p>
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
            const raceSelect = form.querySelector('#race_id');
            const classSelect = form.querySelector('#class_id');
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
            let armorClassIsManual = armorClassModeInput?.value === 'manual';

            const labelize = (item) => String(item).replaceAll('-', ' ');
            const translate = (item) => rules.abilityNames[item] ?? rules.ruleLabels[item] ?? labelize(item);

            const readable = (items) => {
                if (!items || items.length === 0) {
                    return 'нет';
                }

                return items
                    .map(translate)
                    .join(', ') || 'на выбор';
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
                const input = form.querySelector(`[data-ability-input="${field}"]`);
                const bonus = Number(race?.ability_bonuses?.[field] ?? 0);

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

            const updateAbilities = () => {
                const race = rules.races[raceSelect.value];
                const bonuses = race?.ability_bonuses ?? {};

                form.querySelectorAll('[data-ability-input]').forEach((input) => {
                    const field = input.dataset.abilityInput;
                    const bonus = Number(bonuses[field] ?? 0);
                    const total = totalAbility(field);
                    const target = form.querySelector(`[data-ability-summary="${field}"]`);

                    if (target) {
                        target.textContent = signed(modifier(total));
                        target.title = `${bonus ? `Бонус расы ${signed(bonus)}. ` : ''}Итоговое значение ${total}, модификатор ${signed(modifier(total))}.`;
                    }
                });
            };

            const updateRules = () => {
                clampCharacterNumbers();

                const race = rules.races[raceSelect.value];
                const characterClass = rules.classes[classSelect.value];
                const background = rules.backgrounds[backgroundSelect.value];

                if (race) {
                    speedInput.value = Math.min(100, Number(race.speed || 0));
                }

                if (characterClass && Number(maxHpInput.value || 0) === 0) {
                    const hp = Math.min(100, Math.max(1, Number(characterClass.hit_die) + modifier(totalAbility('constitution'))));
                    maxHpInput.value = hp;
                    currentHpInput.value = hp;
                }

                if (armorClassInput && !armorClassIsManual) {
                    armorClassInput.value = Math.min(30, baseArmorClass());
                }

                if (race) {
                    applyChecked('[data-language-input', race.languages ?? []);
                }

                if (background) {
                    applyChecked('[data-skill-input', background.skill_proficiencies ?? []);
                    applyChecked('[data-language-input', background.languages ?? []);

                    if (equipmentText && equipmentText.value.trim() === '' && background.equipment?.length) {
                        equipmentText.value = background.equipment.map(translate).join("\n");
                    }
                }

                form.querySelectorAll('[data-save-ability]').forEach((label) => {
                    const active = characterClass?.saving_throws?.includes(label.dataset.saveAbility);
                    label.classList.toggle('is-proficient', Boolean(active));
                });

                const features = [
                    ...(race?.features ?? []),
                    ...(characterClass?.features ?? []),
                    ...(background?.features ?? []),
                ];

                form.querySelector('[data-rule-race]').textContent = race
                    ? `${race.name}, скорость ${race.speed}, размер ${race.size ?? '—'}, языки: ${readable(race.languages)}`
                    : 'выбери расу';
                form.querySelector('[data-rule-class]').textContent = characterClass
                    ? `${characterClass.name}, кость хитов d${characterClass.hit_die}, спасброски: ${readable(characterClass.saving_throws)}`
                    : 'выбери класс';
                form.querySelector('[data-rule-background]').textContent = background
                    ? `${background.name}, навыки: ${readable(background.skill_proficiencies)}, языки: ${readable(background.languages)}`
                    : 'выбери предысторию';
                form.querySelector('[data-rule-features]').textContent = readable([...new Set(features)]);

                clampCharacterNumbers();
                updateAbilities();
            };

            form.querySelectorAll('input[type="number"]').forEach((input) => {
                input.addEventListener('input', clampCharacterNumbers);
                input.addEventListener('change', clampCharacterNumbers);
            });

            form.addEventListener('submit', clampCharacterNumbers);

            [raceSelect, classSelect, backgroundSelect, levelInput, constitutionInput].forEach((element) => {
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

            document.querySelectorAll('[data-help-title][data-help-body]').forEach((trigger) => {
                trigger.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    helpTitle.textContent = trigger.dataset.helpTitle;
                    helpBody.textContent = trigger.dataset.helpBody;
                    helpOverlay.removeAttribute('hidden');
                    helpClose.focus();
                });
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
