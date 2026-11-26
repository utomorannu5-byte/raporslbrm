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

/**
 * Fokus: watermark DRAFT pada rapor akademik (PDF & Word).
 */
class DraftWatermarkTest extends TestCase
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

    protected function submitCompleteGrades(User $teacher, Student $student, Subject $subject): void
    {
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
    }

    protected function pdfText(string $pdf): string
    {
        $text = $pdf;

        if (preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $matches)) {
            foreach ($matches[1] as $chunk) {
                $decoded = @gzuncompress($chunk);

                if ($decoded !== false) {
                    $text .= "\n".$decoded;
                }
            }
        }

        return $text;
    }

    public function test_pdf_has_draft_watermark_when_not_submitted(): void
    {
        Storage::fake('public');

        ['teacher' => $teacher, 'student' => $student, 'academicYear' => $academicYear] = $this->scenario();

        $this->actingAs($teacher)->get("/guru/report/{$student->id}/academic")->assertOk();

        $path = "reports/academic/pdf/rapor-akademik-{$student->id}-{$academicYear->id}.pdf";
        $this->assertStringContainsString('DRAFT', $this->pdfText(Storage::disk('public')->get($path)));
    }

    public function test_pdf_has_no_draft_watermark_when_submitted(): void
    {
        Storage::fake('public');

        ['teacher' => $teacher, 'student' => $student, 'subject' => $subject, 'academicYear' => $academicYear] = $this->scenario();

        $this->submitCompleteGrades($teacher, $student, $subject);

        $this->actingAs($teacher)->get("/guru/report/{$student->id}/academic")->assertOk();

        $path = "reports/academic/pdf/rapor-akademik-{$student->id}-{$academicYear->id}.pdf";
        $this->assertStringNotContainsString('DRAFT', $this->pdfText(Storage::disk('public')->get($path)));
    }

    public function test_word_has_draft_watermark_when_not_submitted(): void
    {
        Storage::fake('public');

        ['teacher' => $teacher, 'student' => $student, 'academicYear' => $academicYear] = $this->scenario();

        $this->actingAs($teacher)->get("/guru/report/{$student->id}/academic/word")->assertOk();

        $path = "reports/academic/word/rapor-akademik-{$student->id}-{$academicYear->id}.doc";
        $this->assertStringContainsString('DRAFT', Storage::disk('public')->get($path));
    }

    public function test_word_has_no_draft_watermark_when_submitted(): void
    {
        Storage::fake('public');

        ['teacher' => $teacher, 'student' => $student, 'subject' => $subject, 'academicYear' => $academicYear] = $this->scenario();

        $this->submitCompleteGrades($teacher, $student, $subject);

        $this->actingAs($teacher)->get("/guru/report/{$student->id}/academic/word")->assertOk();

        $path = "reports/academic/word/rapor-akademik-{$student->id}-{$academicYear->id}.doc";
        $this->assertStringNotContainsString('DRAFT', Storage::disk('public')->get($path));
    }
}
