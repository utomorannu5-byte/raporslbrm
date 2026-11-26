<?php

namespace Tests\Feature\Guru;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateAcademicWordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    /**
     * @return array{teacher: User, academicYear: AcademicYear, class: ClassRoom, subject: Subject, student: Student}
     */
    protected function scenario(): array
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

        return compact('teacher', 'academicYear', 'class', 'subject', 'student');
    }

    public function test_guru_can_generate_academic_word(): void
    {
        Storage::fake('public');

        ['teacher' => $teacher, 'academicYear' => $academicYear, 'subject' => $subject, 'student' => $student] = $this->scenario();

        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'sick' => 1,
            'permission' => 0,
            'absent' => 0,
        ]);

        $response = $this->actingAs($teacher)->get("/guru/report/{$student->id}/academic/word");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/msword');

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertNotNull($disposition);
        $this->assertStringStartsWith('attachment; filename=', $disposition);

        $path = "reports/academic/word/rapor-akademik-{$student->id}-{$academicYear->id}.doc";
        $this->assertTrue(Storage::disk('public')->exists($path));

        $html = Storage::disk('public')->get($path);
        $this->assertStringContainsString('Laporan Hasil Belajar Siswa', $html);
        $this->assertStringContainsString($student->name, $html);
        $this->assertStringContainsString('Nilai Akademik', $html);

        $this->assertDatabaseHas('report_archives', [
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'type' => 'academic',
            'format' => 'word',
        ]);
    }

    public function test_guru_cannot_access_other_student_word(): void
    {
        ['student' => $student] = $this->scenario();

        $otherTeacher = User::factory()->create()->assignRole('guru');

        $response = $this->actingAs($otherTeacher)->get("/guru/report/{$student->id}/academic/word");

        $response->assertForbidden();
    }

    public function test_word_contains_draft_watermark_when_not_submitted(): void
    {
        Storage::fake('public');

        ['teacher' => $teacher, 'student' => $student, 'academicYear' => $academicYear] = $this->scenario();

        $response = $this->actingAs($teacher)->get("/guru/report/{$student->id}/academic/word");

        $response->assertOk();

        $path = "reports/academic/word/rapor-akademik-{$student->id}-{$academicYear->id}.doc";
        $this->assertStringContainsString('DRAFT', Storage::disk('public')->get($path));
    }

    public function test_word_has_no_draft_watermark_when_submitted(): void
    {
        Storage::fake('public');

        ['teacher' => $teacher, 'student' => $student, 'subject' => $subject, 'academicYear' => $academicYear] = $this->scenario();

        $this->actingAs($teacher)->put("/guru/input-nilai/{$student->id}", [
            'grades' => [$subject->id => ['score_number' => 85]],
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'sick' => 1,
            'permission' => 0,
            'absent' => 0,
        ]);

        $this->actingAs($teacher)->post("/guru/input-nilai/{$student->id}/submit");

        $response = $this->actingAs($teacher)->get("/guru/report/{$student->id}/academic/word");

        $response->assertOk();

        $path = "reports/academic/word/rapor-akademik-{$student->id}-{$academicYear->id}.doc";
        $this->assertStringNotContainsString('DRAFT', Storage::disk('public')->get($path));
    }
}
