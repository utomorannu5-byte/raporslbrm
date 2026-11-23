<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SchoolProfileSeeder::class,
            AcademicYearSeeder::class,
            GradeConversionSeeder::class,
            SubjectSeeder::class,
            CompetencySeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
