<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('race_subraces', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('speed')->nullable();
            $table->json('languages')->nullable();
            $table->json('features')->nullable();
            $table->json('ability_bonuses')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_subraces');
    }
};
