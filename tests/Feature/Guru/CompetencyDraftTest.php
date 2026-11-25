<?php

namespace Tests\Feature\Guru;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\CompetencyGrade;
use App\Models\CompetencyIndicator;
use App\Models\CompetencyDomain;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetencyDraftTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    public function test_guru_can_save_draft_competency(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $domain = CompetencyDomain::factory()->create(['name' => 'Kognitif']);
        $indicator1 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);
        $indicator2 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Berbicara']);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $class->competencyIndicators()->attach([$indicator1->id, $indicator2->id]);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'competencies' => [
                $indicator1->id => 'M',
                $indicator2->id => 'AV',
            ],
        ]);

        $response->assertRedirect()->assertSessionHas('status', 'Draft berhasil disimpan.');

        $this->assertDatabaseHas('competency_grades', [
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'indicator_id' => $indicator1->id,
            'value' => 'M',
            'is_submitted' => false,
        ]);

        $this->assertDatabaseHas('competency_grades', [
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'indicator_id' => $indicator2->id,
            'value' => 'AV',
            'is_submitted' => false,
        ]);
    }

    public function test_guru_can_update_competency_draft(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $domain = CompetencyDomain::factory()->create(['name' => 'Kognitif']);
        $indicator = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $class->competencyIndicators()->attach($indicator);

        $student = Student::factory()->create(['class_id' => $class->id]);

        // First save with M
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'competencies' => [$indicator->id => 'M'],
        ]);

        // Update to AV
        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'competencies' => [$indicator->id => 'AV'],
        ]);

        $response->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('competency_grades', [
            'student_id' => $student->id,
            'indicator_id' => $indicator->id,
            'value' => 'AV',
        ]);
    }

    public function test_guru_can_clear_competency_draft(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $domain = CompetencyDomain::factory()->create(['name' => 'Kognitif']);
        $indicator = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $class->competencyIndicators()->attach($indicator);

        $student = Student::factory()->create(['class_id' => $class->id]);

        // Save with M
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'competencies' => [$indicator->id => 'M'],
        ]);

        // Clear the value
        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'competencies' => [$indicator->id => ''],
        ]);

        $response->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('competency_grades', [
            'student_id' => $student->id,
            'indicator_id' => $indicator->id,
            'value' => null,
        ]);
    }

    public function test_validation_rejects_invalid_competency_value(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $domain = CompetencyDomain::factory()->create(['name' => 'Kognitif']);
        $indicator = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $class->competencyIndicators()->attach($indicator);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'competencies' => [$indicator->id => 'X'], // Invalid
        ]);

        $response->assertSessionHasErrors('competencies.*');
    }

    public function test_guru_cannot_save_competency_for_other_class(): void
    {
        $teacher1 = User::factory()->create()->assignRole('guru');
        $teacher2 = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $domain = CompetencyDomain::factory()->create(['name' => 'Kognitif']);
        $indicator = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher1->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $class->competencyIndicators()->attach($indicator);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher2)->put("/guru/input-nilai/{$student->id}", [
            'competencies' => [$indicator->id => 'M'],
        ]);

        $response->assertForbidden();
    }
}
