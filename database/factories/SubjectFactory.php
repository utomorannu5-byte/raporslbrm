<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS',
                'Pendidikan Agama', 'Pendidikan Jasmani', 'Seni Budaya', 'PKN', 'TI',
            ]).' '.$this->faker->unique()->numberBetween(1, 100),
            'default_kkm' => $this->faker->numberBetween(60, 85),
        ];
    }
}
