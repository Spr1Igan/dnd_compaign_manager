<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('character_classes', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('slug', 120)->unique();

            $table->text('description')->nullable();

            $table->unsignedTinyInteger('hit_die')->default(8);

            $table->json('saving_throws')->nullable();
            $table->json('armor_proficiencies')->nullable();
            $table->json('weapon_proficiencies')->nullable();
            $table->json('tool_proficiencies')->nullable();
            $table->json('skill_choices')->nullable();
            $table->json('features')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_classes');
    }
};
