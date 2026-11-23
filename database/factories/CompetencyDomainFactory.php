<?php

namespace Database\Factories;

use App\Models\CompetencyDomain;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetencyDomainFactory extends Factory
{
    protected $model = CompetencyDomain::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Kognitif', 'Afektif', 'Psikomotorik', 'Bahasa', 'Matematika',
                'IPA', 'IPS', 'Keterampilan Hidup', 'Kesenian', 'Olahraga',
            ]).' '.$this->faker->unique()->numberBetween(1, 100),
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }
}
