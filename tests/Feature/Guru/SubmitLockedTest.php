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

class SubmitLockedTest extends TestCase
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
        $indicator1 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menyimak']);
        $indicator2 = CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Berbicara']);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $class->competencyIndicators()->attach([$indicator1->id, $indicator2->id]);

        $subject = Subject::factory()->create(['default_kkm' => 75]);
        $class->subjects()->attach($subject);

        $student = Student::factory()->create(['class_id' => $class->id]);

        return compact('teacher', 'academicYear', 'class', 'subject', 'domain', 'indicator1', 'indicator2', 'student');
    }

    public function test_submit_succeeds_when_all_complete(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill all academic grades
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'sick' => 1,
            'permission' => 0,
            'absent' => 0,
            'competencies' => [
                $indicator1->id => 'M',
                $indicator2->id => 'AV',
            ],
        ]);

        // Submit
        $response = $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $response->assertRedirect()->assertSessionHas('status', 'Nilai berhasil dikunci. Rapor siap dicetak.');

        // Check all academic grades locked
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'is_submitted' => true,
        ]);

        // Check all competency grades locked
        $this->assertDatabaseHas('competency_grades', [
            'student_id' => $student->id,
            'indicator_id' => $indicator1->id,
            'is_submitted' => true,
        ]);
        $this->assertDatabaseHas('competency_grades', [
            'student_id' => $student->id,
            'indicator_id' => $indicator2->id,
            'is_submitted' => true,
        ]);
    }

    public function test_submit_fails_when_academic_grade_missing(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Only fill competency and attitudes, leave academic grade empty
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [
                $indicator1->id => 'M',
                $indicator2->id => 'AV',
            ],
        ]);

        $response = $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $response->assertRedirect()->assertSessionHas('error');
        $response->assertSessionHas('error', fn($msg) => str_contains($msg, 'nilai akademik yang kosong'));
    }

    public function test_submit_fails_when_attitude_missing(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill academic and competency, leave attitudes empty
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'competencies' => [
                $indicator1->id => 'M',
                $indicator2->id => 'AV',
            ],
        ]);

        $response = $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $response->assertRedirect()->assertSessionHas('error');
        $response->assertSessionHas('error', fn($msg) => str_contains($msg, 'Kepribadian'));
    }

    public function test_submit_fails_when_competency_missing(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill academic and attitudes, leave competency empty
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
        ]);

        $response = $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $response->assertRedirect()->assertSessionHas('error');
        $response->assertSessionHas('error', fn($msg) => str_contains($msg, 'indikator kompetensi yang belum dinilai'));
    }

    public function test_after_submit_edit_page_is_readonly(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill all
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [
                $indicator1->id => 'M',
                $indicator2->id => 'AV',
            ],
        ]);

        // Submit
        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        // Try to access edit page
        $response = $this->actingAs($teacher)->get("/guru/input-nilai/{$student->id}");

        $response->assertOk();
        $response->assertSee('TERKUNCI');
        $response->assertSee('disabled'); // Inputs should be disabled
    }

    public function test_update_after_locked_returns_403(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill all and submit
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [
                $indicator1->id => 'M',
                $indicator2->id => 'AV',
            ],
        ]);

        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        // Try to update
        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 90]],
        ]);

        $response->assertRedirect()->assertSessionHas('error', 'Nilai sudah terkunci, tidak dapat diubah.');
    }

    public function test_submit_twice_returns_error(): void
    {
        $data = $this->setupCompleteScenario();
        extract($data);

        // Fill all and submit
        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [
                $indicator1->id => 'M',
                $indicator2->id => 'AV',
            ],
        ]);

        // First submit
        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        // Second submit
        $response = $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $response->assertRedirect()->assertSessionHas('error', 'Nilai sudah terkunci, tidak dapat diubah.');
    }

    public function test_guru_cannot_submit_other_class_student(): void
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

        $subject = Subject::factory()->create(['default_kkm' => 75]);
        $class->subjects()->attach($subject);

        $student = Student::factory()->create(['class_id' => $class->id]);

        // Fill all for teacher1
        $this->actingAs($teacher1)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'competencies' => [$indicator->id => 'M'],
        ]);

        // Teacher2 tries to submit
        $response = $this->actingAs($teacher2)->post("/guru/input-nilai/{$student->id}/submit");

        $response->assertForbidden();
    }
}
