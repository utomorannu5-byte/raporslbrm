<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_set_active_akademic_year_sets_others_inactive(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $admin = User::factory()->create(['email' => 'testadmin@erapor-slb.test', 'password' => 'password'])
            ->assignRole('admin');

        $this->actingAs($admin)->post('/admin/academic-years', [
            'year' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'year' => '2025/2026',
            'is_active' => true,
        ]);

        $admin2 = User::factory()->create(['email' => 'testadmin2@erapor-slb.test', 'password' => 'password'])
            ->assignRole('admin');

        $this->actingAs($admin2)->post('/admin/academic-years', [
            'year' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'year' => '2026/2027',
            'is_active' => true,
        ]);

        $this->assertDatabaseMissing('academic_years', [
            'year' => '2025/2026',
            'is_active' => true,
        ]);
    }
}