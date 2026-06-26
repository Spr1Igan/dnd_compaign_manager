<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['name' => 'Общий', 'slug' => 'common', 'type' => 'standard'],
            ['name' => 'Дварфский', 'slug' => 'dwarvish', 'type' => 'standard'],
            ['name' => 'Эльфийский', 'slug' => 'elvish', 'type' => 'standard'],
            ['name' => 'Великаний', 'slug' => 'giant', 'type' => 'standard'],
            ['name' => 'Гномий', 'slug' => 'gnomish', 'type' => 'standard'],
            ['name' => 'Гоблинский', 'slug' => 'goblin', 'type' => 'standard'],
            ['name' => 'Полуросликов', 'slug' => 'halfling', 'type' => 'standard'],
            ['name' => 'Орочий', 'slug' => 'orc', 'type' => 'standard'],
            ['name' => 'Бездны', 'slug' => 'abyssal', 'type' => 'exotic'],
            ['name' => 'Небесный', 'slug' => 'celestial', 'type' => 'exotic'],
            ['name' => 'Драконий', 'slug' => 'draconic', 'type' => 'exotic'],
            ['name' => 'Глубинная речь', 'slug' => 'deep-speech', 'type' => 'exotic'],
            ['name' => 'Инфернальный', 'slug' => 'infernal', 'type' => 'exotic'],
            ['name' => 'Первичный', 'slug' => 'primordial', 'type' => 'exotic'],
            ['name' => 'Сильван', 'slug' => 'sylvan', 'type' => 'exotic'],
            ['name' => 'Подземный', 'slug' => 'undercommon', 'type' => 'exotic'],
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(['slug' => $language['slug']], $language);
        }
    }
}
