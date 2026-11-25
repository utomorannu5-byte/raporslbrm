<?php

namespace Tests\Feature\Guru;

use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\CompetencyGrade;
use App\Models\CompetencyIndicator;
use App\Models\CompetencyDomain;
use App\Models\Student;
use App\Models\StudentAttitude;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReSubmitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    protected function setupCompleteScenario(): array
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

        $subject = Subject::factory()->create(['default_kkm' => 75]);
        $class->subjects()->attach($subject);

        $student = Student::factory()->create(['class_id' => $class->id]);

        return compact('teacher', 'academicYear', 'class', 'subject', 'domain', 'indicator', 'student');
    }

    public function test_second_submit_returns_error(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill all
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [$indicator->id => 'M'],
        ]);

        // First submit
        $response = $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");
        $response->assertRedirect()->assertSessionHas('status');

        // Second submit
        $response = $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");
        $response->assertRedirect()->assertSessionHas('error', 'Nilai sudah terkunci, tidak dapat diubah.');
    }

    public function test_edit_after_submit_shows_locked_badge(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill all and submit
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [$indicator->id => 'M'],
        ]);

        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        // Access edit page
        $response = $this->actingAs($teacher)->get("/guru/input-nilai/{$student->id}");

        $response->assertOk();
        $response->assertSee('TERKUNCI');
        $response->assertSee('disabled');
    }

    public function test_academic_grades_remain_locked_after_second_submit_attempt(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [$indicator->id => 'M'],
        ]);

        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        // Try to submit again
        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        // Verify still locked
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $student->id,
            'is_submitted' => true,
        ]);
    }

    public function test_competency_grades_remain_locked_after_second_submit_attempt(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [$indicator->id => 'M'],
        ]);

        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $this->assertDatabaseHas('competency_grades', [
            'student_id' => $student->id,
            'is_submitted' => true,
        ]);
    }
}
