<?php

namespace Tests\Feature\Guru;

use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentAttitude;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InputNilaiDraftTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    public function test_guru_can_save_draft_grades(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $subject = Subject::factory()->create(['default_kkm' => 75]);
        $class->subjects()->attach($subject);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [
                $subject->id => ['score_number' => 85],
            ],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'sick' => 1,
            'permission' => 0,
            'absent' => 0,
        ]);

        $response->assertRedirect()->assertSessionHas('status', 'Draft berhasil disimpan.');

        // Check academic_grades
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'subject_id' => $subject->id,
            'score_number' => 85,
            'score_text' => 'Delapan puluh lima',
            'is_submitted' => false,
        ]);

        // Check student_attitudes
        $this->assertDatabaseHas('student_attitudes', [
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'sick' => 1,
            'permission' => 0,
            'absent' => 0,
        ]);
    }

    public function test_partial_grades_saved(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $subject1 = Subject::factory()->create(['default_kkm' => 75]);
        $subject2 = Subject::factory()->create(['default_kkm' => 70]);
        $class->subjects()->attach([$subject1->id, $subject2->id]);

        $student = Student::factory()->create(['class_id' => $class->id]);

        // Only submit score for subject1
        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [
                $subject1->id => ['score_number' => 90],
            ],
        ]);

        $response->assertRedirect()->assertSessionHas('status');

        // Subject1 should have score
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $student->id,
            'subject_id' => $subject1->id,
            'score_number' => 90,
        ]);

        // Subject2 should not have score
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $student->id,
            'subject_id' => $subject2->id,
            'score_number' => null,
        ]);
    }

    public function test_is_submitted_remains_false_after_draft(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $subject = Subject::factory()->create(['default_kkm' => 75]);
        $class->subjects()->attach($subject);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [
                $subject->id => ['score_number' => 85],
            ],
        ]);

        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $student->id,
            'is_submitted' => false,
        ]);
    }

    public function test_validation_rejects_invalid_score(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $subject = Subject::factory()->create(['default_kkm' => 75]);
        $class->subjects()->attach($subject);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [
                $subject->id => ['score_number' => 150], // Invalid > 100
            ],
        ]);

        $response->assertSessionHasErrors('grades.*.score_number');
    }

    public function test_validation_rejects_invalid_attitude(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'sikap' => 'E', // Invalid
        ]);

        $response->assertSessionHasErrors('sikap');
    }
}
