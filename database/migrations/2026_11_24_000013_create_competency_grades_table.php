<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('competency_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('indicator_id')->constrained('competency_indicators')->cascadeOnDelete();
            $table->enum('value', ['M', 'AV', 'AF', 'AP'])->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'indicator_id'], 'competency_grades_ug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_grades');
    }
};