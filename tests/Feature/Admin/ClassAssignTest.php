<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\CompetencyDomain;
use App\Models\CompetencyIndicator;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassAssignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_assign_subjects_and_indicators_to_class(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
            ->assignRole('admin');

        $subject1 = Subject::factory()->create(['name' => 'Matematika']);
        $subject2 = Subject::factory()->create(['name' => 'Bahasa Indonesia']);

        $domain = CompetencyDomain::factory()->create(['name' => 'Kognitif']);
        $indicator1 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);
        $indicator2 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Berbicara']);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            $activeYear = AcademicYear::factory()->create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);
        }

        // Create class with assign
        $response = $this->actingAs($admin)->post('/admin/classes', [
            'name' => 'Kelas A',
            'academic_year_id' => $activeYear->id,
            'subject_ids' => [$subject1->id, $subject2->id],
            'indicator_ids' => [$indicator1->id, $indicator2->id],
        ]);

        $response->assertRedirect()->assertSessionHas('status');

        $class = ClassRoom::where('name', 'Kelas A')->first();
        $this->assertNotNull($class);
        $this->assertEquals(2, $class->subjects->count());
        $this->assertEquals(2, $class->competencyIndicators->count());
        $this->assertTrue($class->subjects->contains($subject1->id));
        $this->assertTrue($class->subjects->contains($subject2->id));
        $this->assertTrue($class->competencyIndicators->contains($indicator1->id));
        $this->assertTrue($class->competencyIndicators->contains($indicator2->id));
    }

    public function test_sync_removes_unselected_subjects_and_indicators(): void
    {
        $admin = User::factory()->create(['email' => 'admin2@test.test', 'password' => 'password'])
            ->assignRole('admin');

        $subject1 = Subject::factory()->create(['name' => 'Matematika']);
        $subject2 = Subject::factory()->create(['name' => 'Bahasa Indonesia']);
        $subject3 = Subject::factory()->create(['name' => 'IPA']);

        $domain = CompetencyDomain::factory()->create(['name' => 'Kognitif']);
        $indicator1 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);
        $indicator2 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Berbicara']);
        $indicator3 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Membaca']);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            $activeYear = AcademicYear::factory()->create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);
        }

        // Create class with initial assign
        $class = ClassRoom::factory()->create([
            'name' => 'Kelas B',
            'academic_year_id' => $activeYear->id,
        ]);
        $class->subjects()->sync([$subject1->id, $subject2->id]);
        $class->competencyIndicators()->sync([$indicator1->id, $indicator2->id]);

        // Update class - remove subject1, indicator1, add subject3, indicator3
        $this->actingAs($admin)->put("/admin/classes/{$class->id}", [
            'name' => 'Kelas B',
            'academic_year_id' => $activeYear->id,
            'subject_ids' => [$subject2->id, $subject3->id],
            'indicator_ids' => [$indicator2->id, $indicator3->id],
        ]);

        $class->refresh();
        $this->assertEquals(2, $class->subjects->count());
        $this->assertEquals(2, $class->competencyIndicators->count());
        $this->assertFalse($class->subjects->contains($subject1->id));
        $this->assertTrue($class->subjects->contains($subject2->id));
        $this->assertTrue($class->subjects->contains($subject3->id));
        $this->assertFalse($class->competencyIndicators->contains($indicator1->id));
        $this->assertTrue($class->competencyIndicators->contains($indicator2->id));
        $this->assertTrue($class->competencyIndicators->contains($indicator3->id));
    }
}
