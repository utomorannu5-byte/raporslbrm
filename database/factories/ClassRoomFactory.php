<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassRoomFactory extends Factory
{
    protected $model = ClassRoom::class;

    public function definition(): array
    {
        return [
            'name' => 'Kelas '.$this->faker->randomElement(['A', 'B', 'C', 'D']).' '.$this->faker->randomElement(['SD', 'SMP', 'SMA', 'Lanjutan', 'Vokasional']),
            'academic_year_id' => AcademicYear::inRandomOrder()->first()?->id ?? AcademicYear::factory(),
            'teacher_id' => User::role('guru')->inRandomOrder()->first()?->id,
        ];
    }
}
