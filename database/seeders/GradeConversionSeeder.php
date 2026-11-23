<?php

namespace Database\Seeders;

use App\Models\GradeConversion;
use Illuminate\Database\Seeder;

class GradeConversionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            ['min_score' => 0, 'max_score' => 9, 'text_label' => 'Nol'],
            ['min_score' => 10, 'max_score' => 19, 'text_label' => 'Sepuluh'],
            ['min_score' => 20, 'max_score' => 29, 'text_label' => 'Dua puluh'],
            ['min_score' => 30, 'max_score' => 39, 'text_label' => 'Tiga puluh'],
            ['min_score' => 40, 'max_score' => 49, 'text_label' => 'Empat puluh'],
            ['min_score' => 50, 'max_score' => 59, 'text_label' => 'Lima puluh'],
            ['min_score' => 60, 'max_score' => 69, 'text_label' => 'Enam puluh'],
            ['min_score' => 70, 'max_score' => 79, 'text_label' => 'Tujuh puluh'],
            ['min_score' => 80, 'max_score' => 89, 'text_label' => 'Delapan puluh'],
            ['min_score' => 90, 'max_score' => 100, 'text_label' => 'Sembilan puluh sembilan'],
        ];

        foreach ($mappings as $mapping) {
            GradeConversion::create($mapping);
        }
    }
}
