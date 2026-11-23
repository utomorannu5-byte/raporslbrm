<?php

namespace Database\Factories;

use App\Models\GradeConversion;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeConversionFactory extends Factory
{
    protected $model = GradeConversion::class;

    public function definition(): array
    {
        $min = $this->faker->numberBetween(0, 90);
        $max = $this->faker->numberBetween($min + 1, 100);

        return [
            'min_score' => $min,
            'max_score' => $max,
            'text_label' => $this->faker->unique()->randomElement([
                'Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang',
                'Istimewa', 'Memuaskan', 'Perlu Bimbingan', 'Belum Mampu', 'Tidak Tercapai',
            ]).' '.$this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
