<?php

namespace Tests\Feature\Guru;

use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InputNilaiLockedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    public function test_locked_grades_cannot_be_updated(): void
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

        // Create submitted grades
        AcademicGrade::create([
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'subject_id' => $subject->id,
            'kkm' => 75,
            'score_number' => 80,
            'score_text' => 'Delapan puluh',
            'is_submitted' => true,
        ]);

        $response = $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [
                $subject->id => ['score_number' => 90],
            ],
        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect();

        // Grade should not be updated
        $this->assertDatabaseHas('academic_grades', [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'score_number' => 80, // Original value
            'is_submitted' => true,
        ]);
    }

    public function test_locked_page_shows_locked_badge(): void
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

        AcademicGrade::create([
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'subject_id' => $subject->id,
            'kkm' => 75,
            'score_number' => 80,
            'score_text' => 'Delapan puluh',
            'is_submitted' => true,
        ]);

        $response = $this->actingAs($teacher)->get("/guru/input-nilai/{$student->id}");

        $response->assertOk();
        $response->assertSee('TERKUNCI');
        $response->assertSee('disabled'); // Inputs should be disabled
    }

    public function test_index_shows_locked_status(): void
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

        AcademicGrade::create([
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'subject_id' => $subject->id,
            'kkm' => 75,
            'score_number' => 80,
            'score_text' => 'Delapan puluh',
            'is_submitted' => true,
        ]);

        $response = $this->actingAs($teacher)->get('/guru/input-nilai');

        $response->assertOk();
        $response->assertSee('Terkunci');
    }
}
