<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'Pendidikan Agama',
            'PKn',
            'B. Indonesia',
            'Matematika',
            'IPA',
            'IPS',
            'Seni Budaya',
            'PJOK',
            'TIK',
            'Mulok',
        ];

        foreach ($subjects as $name) {
            Subject::create(['name' => $name, 'default_kkm' => 75]);
        }
    }
}