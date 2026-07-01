@php
    $abilities = [
        'strength' => [__('game.abilities.strength.name'), __('game.abilities.strength.abbr'), $character->totalAbilityScore('strength'), $character->strength_modifier],
        'dexterity' => [__('game.abilities.dexterity.name'), __('game.abilities.dexterity.abbr'), $character->totalAbilityScore('dexterity'), $character->dexterity_modifier],
        'constitution' => [__('game.abilities.constitution.name'), __('game.abilities.constitution.abbr'), $character->totalAbilityScore('constitution'), $character->constitution_modifier],
        'intelligence' => [__('game.abilities.intelligence.name'), __('game.abilities.intelligence.abbr'), $character->totalAbilityScore('intelligence'), $character->intelligence_modifier],
        'wisdom' => [__('game.abilities.wisdom.name'), __('game.abilities.wisdom.abbr'), $character->totalAbilityScore('wisdom'), $character->wisdom_modifier],
        'charisma' => [__('game.abilities.charisma.name'), __('game.abilities.charisma.abbr'), $character->totalAbilityScore('charisma'), $character->charisma_modifier],
    ];

    $skillNames = collect($character->skill_proficiencies ?? [])
        ->map(fn (string $slug) => \App\Models\Character::readableRuleLabel($slug))
        ->implode(', ');

    $languageNames = collect($character->language_proficiencies ?? [])
        ->map(fn (string $slug) => \App\Models\Character::readableRuleLabel($slug))
        ->implode(', ');

    $featureItems = collect($character->features ?? [])
        ->unique()
        ->values();
    $featureHelp = __('game.feature_help');
    $featureHelpFallback = __('sheet.js.feature_help_fallback');

    $choiceOnlyProficiencies = [
        'three-musical-instruments',
        'one-artisan-tool-or-musical-instrument',
    ];

    $armorProficiencyNames = collect($character->characterClass?->armor_proficiencies ?? [])
        ->map(fn (string $slug) => \App\Models\Character::readableRuleLabel("armor:{$slug}"))
        ->merge($character->custom_armor_proficiencies ?? [])
        ->unique()
        ->implode(', ');

    $weaponProficiencyNames = collect($character->characterClass?->weapon_proficiencies ?? [])
        ->map(fn (string $slug) => \App\Models\Character::readableRuleLabel($slug))
        ->merge($character->custom_weapon_proficiencies ?? [])
        ->unique()
        ->implode(', ');

    $toolProficiencyNames = collect()
        ->merge($character->characterClass?->tool_proficiencies ?? [])
        ->merge($character->background?->tool_proficiencies ?? [])
        ->filter(fn (string $slug): bool => ! str_starts_with($slug, 'choose:') && ! in_array($slug, $choiceOnlyProficiencies, true))
        ->map(fn (string $slug) => \App\Models\Character::readableRuleLabel($slug))
        ->merge($character->custom_tool_proficiencies ?? [])
        ->unique()
        ->implode(', ');

    $savingThrows = collect($character->characterClass?->saving_throws ?? [])
        ->map(fn (string $ability) => [
            'name' => $abilities[$ability][0] ?? $ability,
            'modifier' => $character->savingThrowModifier($ability),
        ]);

    $savingThrowHelp = __('sheet.help.saving_throws');
    $armorClassHelp = __('sheet.help.armor_class');
    $abilityHelp = __('sheet.ability_help');
@endphp

@extends('layouts.app')

@section('title', $character->name)

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">{{ __('ui.show.sheet') }}</p>
        <h1>{{ $character->name }}</h1>
        <p>
            {{ $character->characterClass ? \App\Models\Character::readableRuleLabel($character->characterClass->slug) : __('ui.characters_page.no_class') }}
            @if ($character->characterSubclass)
                / {{ \App\Models\Character::readableRuleLabel($character->characterSubclass->slug) }}
            @endif
            &middot; {{ __('ui.show.level') }} {{ $character->level }}
            &middot; {{ $character->race ? \App\Models\Character::readableRuleLabel($character->race->slug) : __('ui.characters_page.no_race') }}
            @if ($character->subrace)
                / {{ \App\Models\Character::readableRuleLabel($character->subrace->slug) }}
            @endif
        </p>
    </div>

    <div class="actions-row">
        <a class="paper-button" href="{{ route('characters.edit', $character) }}">
            {{ __('ui.edit') }}
        </a>

        <form method="POST" action="{{ route('characters.destroy', $character) }}">
            @csrf
            @method('DELETE')

            <button class="paper-button danger" type="button" data-confirm-target="delete-character-confirm">
                {{ __('ui.delete') }}
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
            <span>{{ __('ui.form.class') }}</span>
            <strong>{{ $character->characterClass ? \App\Models\Character::readableRuleLabel($character->characterClass->slug) : __('ui.dash') }}</strong>
        </div>

        <div class="banner-field">
            <span>{{ __('ui.form.subclass') }}</span>
            <strong>{{ $character->characterSubclass ? \App\Models\Character::readableRuleLabel($character->characterSubclass->slug) : __('ui.dash') }}</strong>
        </div>

        <div class="banner-field banner-level">
            <span>{{ __('ui.form.level') }}</span>
            <strong>{{ $character->level }}</strong>
        </div>

        <div class="banner-name">
            <span>{{ __('ui.form.name') }}</span>
            <strong>{{ $character->name }}</strong>
        </div>

        <div class="banner-field">
            <span>{{ __('ui.form.background') }}</span>
            <strong>{{ $character->background ? \App\Models\Character::readableRuleLabel($character->background->slug) : __('ui.dash') }}</strong>
        </div>

        <div class="banner-field">
            <span>{{ __('ui.form.alignment') }}</span>
            <strong>{{ $character->alignment ?: __('ui.dash') }}</strong>
        </div>
    </section>

    <section class="sheet-quick-row">
        <div><span>{{ __('ui.form.race') }}</span><strong>{{ $character->race ? \App\Models\Character::readableRuleLabel($character->race->slug) : __('ui.dash') }}</strong></div>
        <div><span>{{ __('ui.form.subrace') }}</span><strong>{{ $character->subrace ? \App\Models\Character::readableRuleLabel($character->subrace->slug) : __('ui.dash') }}</strong></div>
        <div><span>{{ __('ui.form.player') }}</span><strong>{{ $character->player_name ?: __('ui.dash') }}</strong></div>
        <div class="experience-field readonly-experience" data-experience-panel>
            <span>{{ __('ui.form.experience') }}</span>
            <strong data-experience-value>{{ $character->experience }}</strong>
            <button
                class="sheet-arrow-button"
                type="button"
                data-experience-toggle
                aria-expanded="false"
                aria-label="{{ __('ui.show.change_experience') }}"
            >&rsaquo;</button>

            <section class="experience-popover" data-experience-popover hidden>
                <label>
                    <span>{{ __('ui.show.amount_experience') }}</span>
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
        @foreach ($abilities as $field => [$label, $abbr, $score, $modifier])
            <div class="sheet-ability-card readonly">
                <button
                    class="sheet-help-trigger"
                    type="button"
                    data-help-title="{{ $label }}"
                    data-help-body="{{ $abilityHelp[$field] }}"
                    aria-label="{{ __('sheet.labels.hint_for', ['name' => $label]) }}"
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
                    {{ __('ui.show.proficiencies') }}
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="{{ __('ui.show.proficiency_bonus') }}"
                        data-help-template="proficiency-help-template"
                        aria-label="{{ __('sheet.labels.hint_for', ['name' => __('ui.show.proficiency_bonus')]) }}"
                    >?</button>
                </h2>
                <p><b>{{ __('ui.form.skills') }}:</b> {{ $skillNames ?: __('ui.dash') }}</p>
                <p><b>{{ __('ui.form.languages') }}:</b> {{ $languageNames ?: __('ui.dash') }}</p>
                <p><b>{{ __('ui.form.armor_proficiencies') }}:</b> {{ $armorProficiencyNames ?: __('ui.dash') }}</p>
                <p><b>{{ __('ui.form.weapon_proficiencies') }}:</b> {{ $weaponProficiencyNames ?: __('ui.dash') }}</p>
                <p><b>{{ __('ui.form.tool_proficiencies') }}:</b> {{ $toolProficiencyNames ?: __('ui.dash') }}</p>
            </section>

            <section class="sheet-panel compact">
                <h2>
                    {{ __('ui.form.saving_throws') }}
                    <button
                        class="sheet-help-trigger section-help"
                        type="button"
                        data-help-title="{{ __('ui.form.saving_throws') }}"
                        data-help-body="{{ $savingThrowHelp }}"
                        aria-label="{{ __('sheet.labels.hint_for', ['name' => __('ui.form.saving_throws')]) }}"
                    >?</button>
                </h2>
                @forelse ($savingThrows as $save)
                    <p class="sheet-line">{{ $save['name'] }} {{ $save['modifier'] >= 0 ? '+' : '' }}{{ $save['modifier'] }}</p>
                @empty
                    <p>{{ __('ui.show.class_not_selected') }}</p>
                @endforelse
            </section>

            <section class="sheet-panel compact">
                <h2>{{ __('ui.form.equipment') }}</h2>
                @forelse ($character->equipment ?? [] as $item)
                    <p class="sheet-line">{{ $item }}</p>
                @empty
                    <p>{{ __('ui.show.empty') }}</p>
                @endforelse
            </section>
        </aside>

        <div class="sheet-core">
            <section class="sheet-panel combat-panel">
                <h2>{{ __('ui.form.combat') }}</h2>
                <div class="combat-stat-grid">
                    <div class="circle-stat readonly">
                        <button
                            class="sheet-help-trigger stat-help"
                            type="button"
                            data-help-title="{{ __('ui.form.armor_class') }}"
                            data-help-body="{{ $armorClassHelp }}"
                            aria-label="{{ __('sheet.labels.hint_for', ['name' => __('ui.form.armor_class')]) }}"
                        >?</button>
                        <span>{{ __('ui.form.armor_class') }}</span>
                        <strong>{{ $character->effectiveArmorClass() }}</strong>
                    </div>
                    <div class="circle-stat readonly"><span>{{ __('ui.form.speed') }}</span><strong>{{ $character->speed }}</strong></div>
                    <div class="hp-box readonly hit-points-box">
                        <span>HP</span>
                        <div class="hp-control">
                            <button class="sheet-arrow-button" type="button" data-hp-step="-1" aria-label="{{ __('ui.show.decrease_hp') }}">-</button>
                            <strong class="hp-fraction">
                                <span data-current-hp>{{ $character->current_hp }}</span>
                                <span class="hp-divider" aria-hidden="true"></span>
                                <span data-max-hp>{{ $character->max_hp }}</span>
                            </strong>
                            <button class="sheet-arrow-button" type="button" data-hp-step="1" aria-label="{{ __('ui.show.increase_hp') }}">+</button>
                        </div>
                    </div>
                    <div class="hp-box readonly"><span>{{ __('ui.show.proficiency_bonus') }}</span><strong>+{{ $character->proficiency_bonus }}</strong></div>
                </div>
            </section>

            <section class="sheet-panel lined-panel readonly-notes">
                <h2>{{ __('ui.form.traits_and_abilities') }}</h2>
                <p>
                    <b>{{ __('ui.form.features') }}:</b>
                    @if ($featureItems->isEmpty())
                        {{ __('ui.dash') }}
                    @else
                        <span class="feature-chip-list">
                            @foreach ($featureItems as $feature)
                                @php
                                    $featureName = \App\Models\Character::readableRuleLabel($feature);
                                    $featureBody = $featureHelp[$feature] ?? str_replace(':feature', $featureName, $featureHelpFallback);
                                @endphp
                                <button
                                    class="feature-chip"
                                    type="button"
                                    data-help-title="{{ $featureName }}"
                                    data-help-body="{{ $featureBody }}"
                                    aria-label="{{ __('sheet.labels.hint_for', ['name' => $featureName]) }}"
                                >{{ $featureName }}</button>
                            @endforeach
                        </span>
                    @endif
                </p>
                <p><b>{{ __('ui.form.personality_traits') }}:</b> {{ $character->personality_traits ?: __('ui.dash') }}</p>
                <p><b>{{ __('ui.form.ideals') }}:</b> {{ $character->ideals ?: __('ui.dash') }}</p>
                <p><b>{{ __('ui.form.bonds') }}:</b> {{ $character->bonds ?: __('ui.dash') }}</p>
                <p><b>{{ __('ui.form.flaws') }}:</b> {{ $character->flaws ?: __('ui.dash') }}</p>
            </section>

            <section class="sheet-panel lined-panel readonly-notes">
                <h2>{{ __('ui.form.backstory') }}</h2>
                <p>{{ $character->backstory ?: __('ui.show.story_empty') }}</p>
            </section>
        </div>
    </div>
</article>

<div class="sheet-help-overlay" data-sheet-help-overlay hidden>
    <section class="sheet-help-popover" role="dialog" aria-modal="true" aria-labelledby="sheet-help-title">
        <button class="sheet-help-close" type="button" data-sheet-help-close aria-label="{{ __('ui.form.close_hint') }}">×</button>
        <p class="eyebrow">{{ __('ui.form.sheet_hint') }}</p>
        <h2 id="sheet-help-title" data-sheet-help-title></h2>
        <div class="sheet-help-content" data-sheet-help-body></div>
    </section>
</div>

<template id="proficiency-help-template">
    <p>{{ __('sheet.proficiency.body') }}</p>

    <table class="sheet-help-table">
        <thead>
            <tr>
                <th>{{ __('sheet.proficiency.level') }}</th>
                <th>{{ __('sheet.proficiency.bonus') }}</th>
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

    <p><b>{{ __('ui.form.skills') }}:</b> {{ __('sheet.proficiency.skill_formula') }}</p>
    <p><b>{{ __('ui.form.saving_throws') }}:</b> {{ __('sheet.proficiency.save_formula') }}</p>
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
        'title' => __('ui.show.delete_title'),
        'message' => __('ui.show.delete_message', ['name' => $character->name]),
        'confirmText' => __('ui.show.delete_confirm'),
        'cancelText' => __('ui.show.delete_cancel'),
    ])
@endpush
