<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            SkillSeeder::class,
            LanguageSeeder::class,
            RaceSeeder::class,
            RaceSubraceSeeder::class,
            CharacterClassSeeder::class,
            CharacterSubclassSeeder::class,
            BackgroundSeeder::class,
        ]);
        User::query()->firstOrCreate([
            'login' => 'test',
        ], [
            'name' => 'Test User',
            'password' => 'password',
        ]);
    }
}
