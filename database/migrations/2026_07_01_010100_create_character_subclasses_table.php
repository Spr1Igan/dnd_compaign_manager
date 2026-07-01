<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_subclasses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->constrained('character_classes')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();
            $table->json('features_by_level')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_subclasses');
    }
};
