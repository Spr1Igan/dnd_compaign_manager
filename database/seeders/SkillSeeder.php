<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'Атлетика', 'slug' => 'athletics', 'ability' => 'strength'],
            ['name' => 'Акробатика', 'slug' => 'acrobatics', 'ability' => 'dexterity'],
            ['name' => 'Ловкость рук', 'slug' => 'sleight-of-hand', 'ability' => 'dexterity'],
            ['name' => 'Скрытность', 'slug' => 'stealth', 'ability' => 'dexterity'],
            ['name' => 'Магия', 'slug' => 'arcana', 'ability' => 'intelligence'],
            ['name' => 'История', 'slug' => 'history', 'ability' => 'intelligence'],
            ['name' => 'Анализ', 'slug' => 'investigation', 'ability' => 'intelligence'],
            ['name' => 'Природа', 'slug' => 'nature', 'ability' => 'intelligence'],
            ['name' => 'Религия', 'slug' => 'religion', 'ability' => 'intelligence'],
            ['name' => 'Уход за животными', 'slug' => 'animal-handling', 'ability' => 'wisdom'],
            ['name' => 'Проницательность', 'slug' => 'insight', 'ability' => 'wisdom'],
            ['name' => 'Медицина', 'slug' => 'medicine', 'ability' => 'wisdom'],
            ['name' => 'Внимательность', 'slug' => 'perception', 'ability' => 'wisdom'],
            ['name' => 'Выживание', 'slug' => 'survival', 'ability' => 'wisdom'],
            ['name' => 'Обман', 'slug' => 'deception', 'ability' => 'charisma'],
            ['name' => 'Запугивание', 'slug' => 'intimidation', 'ability' => 'charisma'],
            ['name' => 'Выступление', 'slug' => 'performance', 'ability' => 'charisma'],
            ['name' => 'Убеждение', 'slug' => 'persuasion', 'ability' => 'charisma'],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(['slug' => $skill['slug']], $skill);
        }
    }
}
