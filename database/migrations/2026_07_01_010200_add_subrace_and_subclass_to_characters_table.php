<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table): void {
            $table->foreignId('subrace_id')
                ->nullable()
                ->after('race_id')
                ->constrained('race_subraces')
                ->nullOnDelete();

            $table->foreignId('subclass_id')
                ->nullable()
                ->after('class_id')
                ->constrained('character_subclasses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('subrace_id');
            $table->dropConstrainedForeignId('subclass_id');
        });
    }
};
