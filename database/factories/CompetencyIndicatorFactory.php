<?php

namespace Database\Factories;

use App\Models\CompetencyDomain;
use App\Models\CompetencyIndicator;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetencyIndicatorFactory extends Factory
{
    protected $model = CompetencyIndicator::class;

    public function definition(): array
    {
        return [
            'domain_id' => CompetencyDomain::inRandomOrder()->first()?->id ?? CompetencyDomain::factory(),
            'name' => $this->faker->unique()->randomElement([
                'Menyimak', 'Berbicara', 'Membaca', 'Menulis', 'Menghitung',
                'Mengamati', 'Mengklasifikasikan', 'Mengukur', 'Mengkomunikasikan', 'Memecahkan Masalah',
            ]).' '.$this->faker->unique()->numberBetween(1, 100),
            'sort_order' => $this->faker->numberBetween(1, 30),
        ];
    }
}
