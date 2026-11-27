<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\CompetencyGrade;
use App\Models\Student;
use App\Models\StudentAttitude;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcademicYearCopyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    protected function setupScenario(): array
    {
        $admin = User::factory()->create()->assignRole('admin');

        // Create source academic year
        $fromYear = AcademicYear::factory()->create([
            'year' => '2025/2026',
            'semester' => 'Ganjil',
        ]);

        // Create destination academic year
        $toYear = AcademicYear::factory()->create([
            'year' => '2025/2026',
            'semester' => 'Genap',
        ]);

        // Create teacher
        $teacher = User::factory()->create()->assignRole('guru');

        // Create class in source year
        $class = ClassRoom::factory()->create([
            'name' => 'XIA',
            'academic_year_id' => $fromYear->id,
            'teacher_id' => $teacher->id,
        ]);

        // Create subjects
        $subject1 = Subject::factory()->create(['name' => 'Matematika', 'default_kkm' => 75]);
        $subject2 = Subject::factory()->create(['name' => 'Bahasa Indonesia', 'default_kkm' => 70]);

        $class->subjects()->sync([$subject1->id, $subject2->id]);

        // Create competency domains & indicators
        $domain = \App\Models\CompetencyDomain::factory()->create(['name' => 'Berbahasa']);
        $indicator1 = \App\Models\CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Membaca']);
        $indicator2 = \App\Models\CompetencyIndicator::factory()->create(['domain_id' => $domain->id, 'name' => 'Menulis']);

        $class->competencyIndicators()->sync([$indicator1->id, $indicator2->id]);

        // Create students in source class
        $student1 = Student::factory()->create(['class_id' => $class->id, 'name' => 'Siswa 1']);
        $student2 = Student::factory()->create(['class_id' => $class->id, 'name' => 'Siswa 2']);

        // Add grades (should NOT be copied)
        AcademicGrade::create([
            'student_id' => $student1->id,
            'academic_year_id' => $fromYear->id,
            'subject_id' => $subject1->id,
            'score_number' => 85,
            'score_text' => '85',
            'kkm' => 75,
            'is_submitted' => true,
        ]);

        CompetencyGrade::create([
            'student_id' => $student1->id,
            'academic_year_id' => $fromYear->id,
            'indicator_id' => $indicator1->id,
            'value' => 'M',
            'is_submitted' => true,
        ]);

        StudentAttitude::create([
            'student_id' => $student1->id,
            'academic_year_id' => $fromYear->id,
            'sikap' => 'A',
            'kerajinan' => 'B',
            'kebersihan' => 'A',
            'sick' => 1,
            'permission' => 0,
            'absent' => 0,
        ]);

        return compact('admin', 'fromYear', 'toYear', 'teacher', 'class', 'subject1', 'subject2', 'domain', 'indicator1', 'indicator2', 'student1', 'student2');
    }

    public function test_admin_can_access_copy_form(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/academic-years/copy');

        $response->assertOk();
        $response->assertSee('Copy Data Tahun Ajaran');
        $response->assertSee('Peringatan Penting');
    }

    public function test_guru_cannot_access_copy_form(): void
    {
        $guru = User::factory()->create()->assignRole('guru');

        $response = $this->actingAs($guru)->get('/admin/academic-years/copy');

        $response->assertForbidden();
    }

    public function test_copy_data_success(): void
    {
        $data = $this->setupScenario();
        extract($data);

        $response = $this->actingAs($admin)->post('/admin/academic-years/copy', [
            'from_year_id' => $fromYear->id,
            'to_year_id' => $toYear->id,
        ]);

        $response->assertRedirect(route('admin.academic-years.index'));
        $response->assertSessionHas('status');

        // Verify classes copied
        $newClasses = ClassRoom::where('academic_year_id', $toYear->id)->get();
        $this->assertEquals(1, $newClasses->count());
        $newClass = $newClasses->first();
        $this->assertEquals($class->name, $newClass->name);
        $this->assertEquals($class->teacher_id, $newClass->teacher_id);

        // Verify subjects copied
        $this->assertEquals(2, $newClass->subjects->count());

        // Verify indicators copied
        $this->assertEquals(2, $newClass->competencyIndicators->count());

        // Verify students copied
        $newStudents = Student::where('class_id', $newClass->id)->get();
        $this->assertEquals(2, $newStudents->count());

        // Verify student data copied correctly
        foreach ($newStudents as $student) {
            $sourceStudent = Student::where('class_id', $class->id)->where('name', $student->name)->first();
            $this->assertNotNull($sourceStudent);
            $this->assertEquals($sourceStudent->parent_name, $student->parent_name);
            // NISN should be unique (newly generated)
            $this->assertNotEquals($sourceStudent->nisn, $student->nisn);
        }

        // Verify academic grades NOT copied
        $copiedGrades = AcademicGrade::where('academic_year_id', $toYear->id)->get();
        $this->assertEquals(0, $copiedGrades->count());

        // Verify competency grades NOT copied
        $copiedCompGrades = CompetencyGrade::where('academic_year_id', $toYear->id)->get();
        $this->assertEquals(0, $copiedCompGrades->count());

        // Verify student attitudes NOT copied
        $copiedAttitudes = StudentAttitude::where('academic_year_id', $toYear->id)->get();
        $this->assertEquals(0, $copiedAttitudes->count());
    }

    public function test_copy_fails_if_same_year(): void
    {
        $data = $this->setupScenario();
        extract($data);

        $response = $this->actingAs($admin)->post('/admin/academic-years/copy', [
            'from_year_id' => $fromYear->id,
            'to_year_id' => $fromYear->id,
        ]);

        $response->assertSessionHasErrors('to_year_id');
    }

    public function test_copy_fails_if_destination_has_classes(): void
    {
        $data = $this->setupScenario();
        extract($data);

        // Create a class in destination year
        ClassRoom::factory()->create([
            'academic_year_id' => $toYear->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/academic-years/copy', [
            'from_year_id' => $fromYear->id,
            'to_year_id' => $toYear->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('sudah memiliki kelas', session('error'));
    }

    public function test_copy_rollback_on_error(): void
    {
        $data = $this->setupScenario();
        extract($data);

        // Force an error by making destination year have a class
        ClassRoom::factory()->create(['academic_year_id' => $toYear->id]);

        $initialClassCount = ClassRoom::where('academic_year_id', $toYear->id)->count();
        $initialStudentCount = Student::whereHas('classRoom', fn ($q) => $q->where('academic_year_id', $toYear->id))->count();

        $response = $this->actingAs($admin)->post('/admin/academic-years/copy', [
            'from_year_id' => $fromYear->id,
            'to_year_id' => $toYear->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Verify no partial data was inserted (rollback worked)
        $this->assertEquals($initialClassCount, ClassRoom::where('academic_year_id', $toYear->id)->count());
        $this->assertEquals($initialStudentCount, Student::whereHas('classRoom', fn ($q) => $q->where('academic_year_id', $toYear->id))->count());
    }
}