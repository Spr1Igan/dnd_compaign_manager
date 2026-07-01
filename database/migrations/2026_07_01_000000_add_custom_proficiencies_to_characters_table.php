<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->json('custom_armor_proficiencies')->nullable();
            $table->json('custom_weapon_proficiencies')->nullable();
            $table->json('custom_tool_proficiencies')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn([
                'custom_armor_proficiencies',
                'custom_weapon_proficiencies',
                'custom_tool_proficiencies',
            ]);
        });
    }
};
