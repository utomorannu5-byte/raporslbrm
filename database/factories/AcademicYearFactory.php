<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $year = $this->faker->numberBetween(2020, 2030);
        $nextYear = $year + 1;
        $semester = $this->faker->randomElement(['Ganjil', 'Genap']);

        return [
            'year' => "$year/$nextYear",
            'semester' => $semester,
            'is_active' => false,
        ];
    }
}
