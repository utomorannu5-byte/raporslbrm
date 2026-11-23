<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grade_conversions', function ($table) {
            $table->id();
            $table->integer('min_score');
            $table->integer('max_score');
            $table->string('text_label');
            $table->timestamps();

            $table->unique(['min_score', 'max_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_conversions');
    }
};
