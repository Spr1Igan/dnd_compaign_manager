



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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('race_id')
                ->nullable()
                ->constrained('races')
                ->nullOnDelete();

            $table->foreignId('class_id')
                ->nullable()
                ->constrained('character_classes')
                ->nullOnDelete();

            $table->foreignId('background_id')
                ->nullable()
                ->constrained('backgrounds')
                ->nullOnDelete();

            $table->string('name', 100);
            $table->string('player_name', 100)->nullable();

            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedInteger('experience')->default(0);

            $table->string('alignment', 100)->nullable();

            $table->unsignedTinyInteger('strength')->default(10);
            $table->unsignedTinyInteger('dexterity')->default(10);
            $table->unsignedTinyInteger('constitution')->default(10);
            $table->unsignedTinyInteger('intelligence')->default(10);
            $table->unsignedTinyInteger('wisdom')->default(10);
            $table->unsignedTinyInteger('charisma')->default(10);

            $table->unsignedSmallInteger('max_hp')->default(0);
            $table->unsignedSmallInteger('current_hp')->default(0);
            $table->unsignedTinyInteger('armor_class')->default(10);
            $table->unsignedTinyInteger('speed')->default(30);

            $table->json('skill_proficiencies')->nullable();
            $table->json('language_proficiencies')->nullable();
            $table->json('equipment')->nullable();
            $table->json('features')->nullable();

            $table->text('personality_traits')->nullable();
            $table->text('ideals')->nullable();
            $table->text('bonds')->nullable();
            $table->text('flaws')->nullable();
            $table->longText('backstory')->nullable();

            $table->string('avatar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
