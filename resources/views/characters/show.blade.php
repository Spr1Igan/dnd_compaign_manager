@php
    $abilities = [
        'strength' => ['Сила', 'СИЛ', $character->totalAbilityScore('strength'), $character->strength_modifier],
        'dexterity' => ['Ловкость', 'ЛОВ', $character->totalAbilityScore('dexterity'), $character->dexterity_modifier],
        'constitution' => ['Телосложение', 'ТЕЛ', $character->totalAbilityScore('constitution'), $character->constitution_modifier],
        'intelligence' => ['Интеллект', 'ИНТ', $character->totalAbilityScore('intelligence'), $character->intelligence_modifier],
        'wisdom' => ['Мудрость', 'МДР', $character->totalAbilityScore('wisdom'), $character->wisdom_modifier],
        'charisma' => ['Харизма', 'ХАР', $character->totalAbilityScore('charisma'), $character->charisma_modifier],
    ];

    $skillNames = collect($character->skill_proficiencies ?? [])
        ->map(fn (string $slug) => $skillsBySlug[$slug]->name ?? $slug)
        ->implode(', ');

    $languageNames = collect($character->language_proficiencies ?? [])
        ->map(fn (string $slug) => $languagesBySlug[$slug]->name ?? $slug)
        ->implode(', ');

    $featureNames = collect($character->features ?? [])
        ->map(fn (string $slug) => \App\Models\Character::readableRuleLabel($slug))
        ->implode(', ');

    $savingThrows = collect($character->characterClass?->saving_throws ?? [])
        ->map(fn (string $ability) => [
            'name' => $abilities[$ability][0] ?? $ability,
            'modifier' => $character->savingThrowModifier($ability),
        ]);

    $savingThrowHelp = 'Спасбросок используется, когда персонаж пытается избежать опасности: яда, заклинания, ловушки, страха, падения или другого эффекта. Бросается d20, добавляется модификатор нужной характеристики, а если класс владеет этим спасброском — ещё бонус мастерства.';
    $armorClassHelp = 'Класс Доспеха показывает, насколько трудно попасть по персонажу атакой. Без доспеха и щита базовый КД равен 10 + модификатор Ловкости. Доспехи, щит и отдельные способности могут менять формулу. Если атака врага равна КД или выше, она обычно попадает.';

    $abilityHelp = [
        'Сила' => 'Сила показывает физическую мощь. Используется для атак и урона оружием ближнего боя, Атлетики, переноски тяжестей, прыжков, лазания, толкания и силовых спасбросков.',
        'Ловкость' => 'Ловкость отражает реакцию, равновесие и точность. Влияет на инициативу, КД без тяжёлых доспехов, атаки дальнобойным и фехтовальным оружием, Акробатику, Скрытность и ловкостные спасброски.',
        'Телосложение' => 'Телосложение отвечает за здоровье и выносливость. Модификатор добавляется к хитам за уровень и используется в спасбросках против яда, болезней, истощения и поддержания концентрации.',
        'Интеллект' => 'Интеллект описывает память, обучение и логику. Используется для Магии, Истории, Анализа, Природы, Религии и часто важен для заклинаний волшебника.',
        'Мудрость' => 'Мудрость отражает внимательность, интуицию и силу воли. Используется для Внимательности, Проницательности, Выживания, Медицины, ухода за животными и мудростных спасбросков.',
        'Харизма' => 'Харизма показывает силу личности и влияние. Используется для Обмана, Запугивания, Выступления, Убеждения и часто важна для заклинаний барда, колдуна, паладина и чародея.',
    ];
@endphp

@extends('layouts.app')

@section('title', $character->name)

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">Лист персонажа</p>
        <h1>{{ $character->name }}</h1>
        <p>
            {{ $character->characterClass?->name ?? 'Без класса' }}
            · уровень {{ $character->level }}
            · {{ $character->race?->name ?? 'Без расы' }}
        </p>
    </div>

    <div class="actions-row">
        <a class="paper-button" href="{{ route('characters.edit', $character) }}">
            Редактировать
        </a>

        <form method="POST" action="{{ route('characters.destroy', $character) }}">
            @csrf
            @method('DELETE')

            <button class="paper-button danger" type="button" data-confirm-target="delete-character-confirm">
                Удалить
            </button>
        </form>
    </div>
</div>

@if (session('success'))
    <p class="success-message">{{ session('success') }}</p>
@endif

<article
    class="character-sheet-form dnd-sheet-form readonly-sheet"
    data-vitals-url="{{ route('characters.vitals.update', $character) }}"
    data-vitals-token="{{ csrf_token() }}"
    data-current-hp="{{ $character->current_hp }}"
    data-max-hp="{{ $character->max_hp }}"
    data-experience="{{ $character->experience }}"
>
    <section class="sheet-banner">
        <div class="banner-field banner-class">
            <span>Класс</span>
            <strong>{{ $character->characterClass?->name ?? '—' }}</strong>
        </div>

        <div class="banner-field banner-level">
            <span>Уровень</span>
            <strong>{{ $character->level }}</strong>
        </div>

        <div class="banner-name">
            <span>Имя персонажа</span>
            <strong>{{ $character->name }}</strong>
        </div>

        <div class="banner-field">
            <span>Предыстория</span>
            <strong>{{ $character->background?->name ?? '—' }}</strong>
        </div>

        <div class="banner-field">
            <span>Мировоззрение</span>
            <strong>{{ $character->alignment ?: '—' }}</strong>
        </div>
    </section>

    <section class="sheet-quick-row">
        <div><span>Раса</span><strong>{{ $character->race?->name ?? '—' }}</strong></div>
        <div><span>Игрок</span><strong>{{ $character->player_name ?: '—' }}</strong></div>
        <div class="experience-field readonly-experience" data-experience-panel>
            <span>Опыт</span>
            <strong data-experience-value>{{ $character->experience }}</strong>
            <button
                class="sheet-arrow-button"
                type="button"
                data-experience-toggle
                aria-expanded="false"
                aria-label="Изменить опыт"
            >&rsaquo;</button>

            <section class="experience-popover" data-experience-popover hidden>
                <label>
                    <span>Количество опыта</span>
                    <input type="number" min="0" step="1" value="0" data-experience-amount>
                </label>
                <div class="experience-actions">
                    <button class="paper-button danger compact-button" type="button" data-experience-action="minus">-</button>
                    <button class="paper-button compact-button" type="button" data-experience-action="plus">+</button>
                </div>
            </section>
        </div>
    </section>

    <section class="sheet-abilities-strip">
        @foreach ($abilities as [$label, $abbr, $score, $modifier])
            <div class="sheet-ability-card readonly">
                <button
                    class="sheet-help-trigger"
                    type="button"
                    data-help-title="{{ $label }}"
                    data-help-body="{{ $abilityHelp[$label] }}"
                    aria-label="Подсказка: {{ $label }}"
                >?</button>
                <span>{{ $abbr }}</span>
                <strong>{{ $score }}</strong>
                <small class="ability-name">{{ $label }}</small>
                <small class="ability-modifier">{{ $modifier >= 0 ? '+' : '' }}{{ $modifier }}</small>
            </div>
        @endforeach
    </section>

    <div class="sheet-main-grid">
        <aside class="sheet-sidebar">
            <section class="sheet-panel compact">
                <h2>
                    Владения
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="Бонус мастерства"
                        data-help-template="proficiency-help-template"
                        aria-label="Подсказка: бонус мастерства"
                    >?</button>
                </h2>
                <p><b>Навыки:</b> {{ $skillNames ?: '—' }}</p>
                <p><b>Языки:</b> {{ $languageNames ?: '—' }}</p>
            </section>

            <section class="sheet-panel compact">
                <h2>
                    Спасброски
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="Спасброски"
                        data-help-body="{{ $savingThrowHelp }}"
                        aria-label="Подсказка: спасброски"
                    >?</button>
                </h2>
                @forelse ($savingThrows as $save)
                    <p class="sheet-line">{{ $save['name'] }} {{ $save['modifier'] >= 0 ? '+' : '' }}{{ $save['modifier'] }}</p>
                @empty
                    <p>Класс не выбран.</p>
                @endforelse
            </section>

            <section class="sheet-panel compact">
                <h2>Снаряжение</h2>
                @forelse ($character->equipment ?? [] as $item)
                    <p class="sheet-line">{{ $item }}</p>
                @empty
                    <p>Пока пусто.</p>
                @endforelse
            </section>
        </aside>

        <div class="sheet-core">
            <section class="sheet-panel combat-panel">
                <h2>Бой</h2>
                <div class="combat-stat-grid">
                    <div class="circle-stat readonly">
                        <button
                            class="sheet-help-trigger stat-help"
                            type="button"
                            data-help-title="Класс Доспеха"
                            data-help-body="{{ $armorClassHelp }}"
                            aria-label="Подсказка: класс доспеха"
                        >?</button>
                        <span>КД</span>
                        <strong>{{ $character->effectiveArmorClass() }}</strong>
                    </div>
                    <div class="circle-stat readonly"><span>Скорость</span><strong>{{ $character->speed }}</strong></div>
                    <div class="hp-box readonly hit-points-box">
                        <span>HP</span>
                        <div class="hp-control">
                            <button class="sheet-arrow-button" type="button" data-hp-step="-1" aria-label="Уменьшить текущие HP">-</button>
                            <strong class="hp-fraction">
                                <span data-current-hp>{{ $character->current_hp }}</span>
                                <span class="hp-divider" aria-hidden="true"></span>
                                <span data-max-hp>{{ $character->max_hp }}</span>
                            </strong>
                            <button class="sheet-arrow-button" type="button" data-hp-step="1" aria-label="Увеличить текущие HP">+</button>
                        </div>
                    </div>
                    <div class="hp-box readonly"><span>Бонус мастерства</span><strong>+{{ $character->proficiency_bonus }}</strong></div>
                </div>
            </section>

            <section class="sheet-panel lined-panel readonly-notes">
                <h2>Черты и способности</h2>
                <p><b>Особенности:</b> {{ $featureNames ?: '—' }}</p>
                <p><b>Черты:</b> {{ $character->personality_traits ?: '—' }}</p>
                <p><b>Идеалы:</b> {{ $character->ideals ?: '—' }}</p>
                <p><b>Привязанности:</b> {{ $character->bonds ?: '—' }}</p>
                <p><b>Слабости:</b> {{ $character->flaws ?: '—' }}</p>
            </section>

            <section class="sheet-panel lined-panel readonly-notes">
                <h2>История</h2>
                <p>{{ $character->backstory ?: 'История пока не заполнена.' }}</p>
            </section>
        </div>
    </div>
</article>

<div class="sheet-help-overlay" data-sheet-help-overlay hidden>
    <section class="sheet-help-popover" role="dialog" aria-modal="true" aria-labelledby="sheet-help-title">
        <button class="sheet-help-close" type="button" data-sheet-help-close aria-label="Закрыть подсказку">×</button>
        <p class="eyebrow">Подсказка листа</p>
        <h2 id="sheet-help-title" data-sheet-help-title></h2>
        <div class="sheet-help-content" data-sheet-help-body></div>
    </section>
</div>

<template id="proficiency-help-template">
    <p>
        Бонус мастерства зависит от уровня персонажа. Он добавляется к проверкам навыков и спасброскам только тогда,
        когда персонаж владеет соответствующим навыком или спасброском.
    </p>

    <table class="sheet-help-table">
        <thead>
            <tr>
                <th>Уровень</th>
                <th>Бонус</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>1-4</td><td>+2</td></tr>
            <tr><td>5-8</td><td>+3</td></tr>
            <tr><td>9-12</td><td>+4</td></tr>
            <tr><td>13-16</td><td>+5</td></tr>
            <tr><td>17-20</td><td>+6</td></tr>
        </tbody>
    </table>

    <p><b>Навык:</b> d20 + модификатор характеристики + бонус мастерства при владении навыком.</p>
    <p><b>Спасбросок:</b> d20 + модификатор характеристики + бонус мастерства, если класс владеет этим спасброском.</p>
</template>

@endsection

@push('scripts')
    <script>
        (() => {
            const helpOverlay = document.querySelector('[data-sheet-help-overlay]');
            const helpTitle = helpOverlay?.querySelector('[data-sheet-help-title]');
            const helpBody = helpOverlay?.querySelector('[data-sheet-help-body]');
            const helpClose = helpOverlay?.querySelector('[data-sheet-help-close]');

            const closeHelp = () => helpOverlay?.setAttribute('hidden', '');

            document.querySelectorAll('[data-help-title]').forEach((trigger) => {
                trigger.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    helpTitle.textContent = trigger.dataset.helpTitle;
                    helpBody.textContent = '';

                    if (trigger.dataset.helpTemplate) {
                        const template = document.getElementById(trigger.dataset.helpTemplate);

                        if (template) {
                            helpBody.append(template.content.cloneNode(true));
                        }
                    } else {
                        const paragraph = document.createElement('p');
                        paragraph.textContent = trigger.dataset.helpBody || '';
                        helpBody.append(paragraph);
                    }

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

            const sheet = document.querySelector('[data-vitals-url]');
            const hpValue = sheet?.querySelector('[data-current-hp]');
            const maxHpValue = sheet?.querySelector('[data-max-hp]');
            const experienceValue = sheet?.querySelector('[data-experience-value]');
            const experienceToggle = sheet?.querySelector('[data-experience-toggle]');
            const experiencePopover = sheet?.querySelector('[data-experience-popover]');
            const experienceAmount = sheet?.querySelector('[data-experience-amount]');

            if (!sheet || !hpValue || !experienceValue) {
                return;
            }

            let currentHp = Number(sheet.dataset.currentHp || hpValue.textContent || 0);
            let maxHp = Number(sheet.dataset.maxHp || maxHpValue?.textContent || 0);
            let experience = Number(sheet.dataset.experience || experienceValue.textContent || 0);
            let isSavingVitals = false;

            const renderVitals = () => {
                hpValue.textContent = currentHp;

                if (maxHpValue) {
                    maxHpValue.textContent = maxHp;
                }

                experienceValue.textContent = experience;
                sheet.dataset.currentHp = String(currentHp);
                sheet.dataset.maxHp = String(maxHp);
                sheet.dataset.experience = String(experience);
            };

            const saveVitals = async (nextHp, nextExperience) => {
                if (isSavingVitals) {
                    return;
                }

                isSavingVitals = true;
                sheet.classList.add('is-saving-vitals');

                const previous = { currentHp, maxHp, experience };
                currentHp = Math.max(0, Math.min(maxHp, Number(nextHp)));
                experience = Math.max(0, Number(nextExperience));
                renderVitals();

                try {
                    const response = await fetch(sheet.dataset.vitalsUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': sheet.dataset.vitalsToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            current_hp: currentHp,
                            experience,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('Vitals update failed');
                    }

                    const data = await response.json();
                    currentHp = Number(data.current_hp);
                    maxHp = Number(data.max_hp);
                    experience = Number(data.experience);
                    renderVitals();
                } catch (error) {
                    currentHp = previous.currentHp;
                    maxHp = previous.maxHp;
                    experience = previous.experience;
                    renderVitals();
                    console.error(error);
                } finally {
                    isSavingVitals = false;
                    sheet.classList.remove('is-saving-vitals');
                }
            };

            sheet.querySelectorAll('[data-hp-step]').forEach((button) => {
                button.addEventListener('click', () => {
                    saveVitals(currentHp + Number(button.dataset.hpStep), experience);
                });
            });

            const closeExperiencePopover = () => {
                experiencePopover?.setAttribute('hidden', '');
                experienceToggle?.setAttribute('aria-expanded', 'false');
            };

            experienceToggle?.addEventListener('click', (event) => {
                event.stopPropagation();
                const shouldOpen = experiencePopover?.hasAttribute('hidden');

                if (shouldOpen) {
                    experiencePopover.removeAttribute('hidden');
                    experienceToggle.setAttribute('aria-expanded', 'true');
                    experienceAmount?.focus();
                    return;
                }

                closeExperiencePopover();
            });

            sheet.querySelectorAll('[data-experience-action]').forEach((button) => {
                button.addEventListener('click', () => {
                    const amount = Math.max(0, Number(experienceAmount?.value || 0));
                    const direction = button.dataset.experienceAction === 'plus' ? 1 : -1;

                    saveVitals(currentHp, experience + amount * direction);
                    closeExperiencePopover();
                });
            });

            document.addEventListener('click', (event) => {
                if (experiencePopover && !experiencePopover.hasAttribute('hidden') && !event.target.closest('[data-experience-panel]')) {
                    closeExperiencePopover();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeExperiencePopover();
                }
            });
        })();
    </script>
@endpush

@push('modals')
    @include('partials.confirm-dialog', [
        'id' => 'delete-character-confirm',
        'title' => 'Удалить персонажа?',
        'message' => 'Лист персонажа «' . $character->name . '» будет удалён без возможности восстановления.',
        'confirmText' => 'Да, удалить',
        'cancelText' => 'Оставить',
    ])
@endpush
