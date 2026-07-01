<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Character extends Model
{
    protected $fillable = [
        'user_id',
        'race_id',
        'subrace_id',
        'class_id',
        'subclass_id',
        'background_id',
        'name',
        'player_name',
        'level',
        'experience',
        'alignment',
        'strength',
        'dexterity',
        'constitution',
        'intelligence',
        'wisdom',
        'charisma',
        'max_hp',
        'current_hp',
        'armor_class',
        'speed',
        'skill_proficiencies',
        'language_proficiencies',
        'custom_armor_proficiencies',
        'custom_weapon_proficiencies',
        'custom_tool_proficiencies',
        'equipment',
        'features',
        'personality_traits',
        'ideals',
        'bonds',
        'flaws',
        'backstory',
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'skill_proficiencies' => 'array',
            'language_proficiencies' => 'array',
            'custom_armor_proficiencies' => 'array',
            'custom_weapon_proficiencies' => 'array',
            'custom_tool_proficiencies' => 'array',
            'equipment' => 'array',
            'features' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function subrace(): BelongsTo
    {
        return $this->belongsTo(RaceSubrace::class, 'subrace_id');
    }

    public function characterClass(): BelongsTo
    {
        return $this->belongsTo(CharacterClass::class, 'class_id');
    }

    public function characterSubclass(): BelongsTo
    {
        return $this->belongsTo(CharacterSubclass::class, 'subclass_id');
    }

    public function background(): BelongsTo
    {
        return $this->belongsTo(Background::class);
    }

    public function getStrengthModifierAttribute(): int
    {
        return $this->totalAbilityModifier('strength');
    }

    public function getDexterityModifierAttribute(): int
    {
        return $this->totalAbilityModifier('dexterity');
    }

    public function getConstitutionModifierAttribute(): int
    {
        return $this->totalAbilityModifier('constitution');
    }

    public function getIntelligenceModifierAttribute(): int
    {
        return $this->totalAbilityModifier('intelligence');
    }

    public function getWisdomModifierAttribute(): int
    {
        return $this->totalAbilityModifier('wisdom');
    }

    public function getCharismaModifierAttribute(): int
    {
        return $this->totalAbilityModifier('charisma');
    }

    public function getProficiencyBonusAttribute(): int
    {
        return match (true) {
            $this->level >= 17 => 6,
            $this->level >= 13 => 5,
            $this->level >= 9 => 4,
            $this->level >= 5 => 3,
            default => 2,
        };
    }

    public function baseArmorClass(): int
    {
        return max(1, 10 + $this->totalAbilityModifier('dexterity'));
    }

    public function usesBaseArmorClass(): bool
    {
        return (int) $this->armor_class === 10 || (int) $this->armor_class === $this->baseArmorClass();
    }

    public function effectiveArmorClass(): int
    {
        if ($this->usesBaseArmorClass()) {
            return $this->baseArmorClass();
        }

        return (int) $this->armor_class;
    }

    public static function ruleLabels(): array
    {
        $fallback = [
            'athletics' => 'Атлетика',
            'acrobatics' => 'Акробатика',
            'sleight-of-hand' => 'Ловкость рук',
            'stealth' => 'Скрытность',
            'arcana' => 'Магия',
            'history' => 'История',
            'investigation' => 'Анализ',
            'nature' => 'Природа',
            'religion' => 'Религия',
            'animal-handling' => 'Уход за животными',
            'insight' => 'Проницательность',
            'medicine' => 'Медицина',
            'perception' => 'Внимательность',
            'survival' => 'Выживание',
            'deception' => 'Обман',
            'intimidation' => 'Запугивание',
            'performance' => 'Выступление',
            'persuasion' => 'Убеждение',
            'common' => 'Общий',
            'dwarvish' => 'Дварфский',
            'elvish' => 'Эльфийский',
            'giant' => 'Великаний',
            'gnomish' => 'Гномий',
            'goblin' => 'Гоблинский',
            'halfling' => 'Полуросликов',
            'orc' => 'Орочий',
            'abyssal' => 'Бездны',
            'celestial' => 'Небесный',
            'draconic' => 'Драконий',
            'deep-speech' => 'Глубинная речь',
            'infernal' => 'Инфернальный',
            'primordial' => 'Первичный',
            'sylvan' => 'Сильван',
            'undercommon' => 'Подземный',
            'darkvision' => 'Тёмное зрение',
            'keen-senses' => 'Обострённые чувства',
            'fey-ancestry' => 'Наследие фей',
            'trance' => 'Транс',
            'dwarven-resilience' => 'Дварфская устойчивость',
            'stonecunning' => 'Знание камня',
            'lucky' => 'Удачливость',
            'brave' => 'Храбрость',
            'halfling-nimbleness' => 'Проворство полурослика',
            'draconic-ancestry' => 'Драконье происхождение',
            'breath-weapon' => 'Оружие дыхания',
            'damage-resistance' => 'Сопротивление урону',
            'gnome-cunning' => 'Гномья хитрость',
            'skill-versatility' => 'Разносторонние навыки',
            'menacing' => 'Угрожающий вид',
            'relentless-endurance' => 'Непоколебимая стойкость',
            'savage-attacks' => 'Свирепые атаки',
            'hellish-resistance' => 'Адское сопротивление',
            'infernal-legacy' => 'Инфернальное наследие',
            'rage' => 'Ярость',
            'unarmored-defense' => 'Защита без доспехов',
            'spellcasting' => 'Использование заклинаний',
            'bardic-inspiration' => 'Бардовское вдохновение',
            'divine-domain' => 'Божественный домен',
            'druidic' => 'Друидический язык',
            'fighting-style' => 'Боевой стиль',
            'second-wind' => 'Второе дыхание',
            'martial-arts' => 'Боевые искусства',
            'divine-sense' => 'Божественное чувство',
            'lay-on-hands' => 'Наложение рук',
            'favored-enemy' => 'Избранный враг',
            'natural-explorer' => 'Исследователь природы',
            'expertise' => 'Компетентность',
            'sneak-attack' => 'Скрытая атака',
            'thieves-cant' => 'Воровской жаргон',
            'sorcerous-origin' => 'Чародейское происхождение',
            'otherworldly-patron' => 'Потусторонний покровитель',
            'pact-magic' => 'Магия договора',
            'arcane-recovery' => 'Магическое восстановление',
            'shelter-of-the-faithful' => 'Приют верующих',
            'false-identity' => 'Поддельная личность',
            'criminal-contact' => 'Криминальный контакт',
            'by-popular-demand' => 'Любимец публики',
            'rustic-hospitality' => 'Деревенское гостеприимство',
            'guild-membership' => 'Членство в гильдии',
            'discovery' => 'Открытие',
            'position-of-privilege' => 'Привилегированное положение',
            'wanderer' => 'Странник',
            'researcher' => 'Исследователь',
            'ships-passage' => 'Морской проход',
            'military-rank' => 'Воинское звание',
            'city-secrets' => 'Городские тайны',
            'disguise-kit' => 'Набор для грима',
            'forgery-kit' => 'Набор фальсификатора',
            'thieves-tools' => 'Воровские инструменты',
            'artisan-tools' => 'Инструменты ремесленника',
            'musical-instrument' => 'Музыкальный инструмент',
            'gaming-set' => 'Игровой набор',
            'vehicles-land' => 'Наземный транспорт',
            'vehicles-water' => 'Водный транспорт',
            'herbalism-kit' => 'Набор травника',
            'navigator-tools' => 'Инструменты навигатора',
        ];

        $translated = trans('game.labels');

        return is_array($translated) ? array_replace($fallback, $translated) : $fallback;
    }

    public static function readableRuleLabel(string $value): string
    {
        return self::ruleLabels()[$value] ?? str($value)->replace('-', ' ')->trim()->toString();
    }

    public function abilityBonus(string $ability): int
    {
        return (int) ($this->race?->ability_bonuses[$ability] ?? 0)
            + (int) ($this->subrace?->ability_bonuses[$ability] ?? 0);
    }

    public function totalAbilityScore(string $ability): int
    {
        return min(30, max(1, (int) $this->{$ability} + $this->abilityBonus($ability)));
    }

    public function totalAbilityModifier(string $ability): int
    {
        return (int) floor(($this->totalAbilityScore($ability) - 10) / 2);
    }

    public function savingThrowModifier(string $ability): int
    {
        $modifier = $this->totalAbilityModifier($ability);

        if (in_array($ability, $this->characterClass?->saving_throws ?? [], true)) {
            $modifier += $this->proficiency_bonus;
        }

        return $modifier;
    }
}
