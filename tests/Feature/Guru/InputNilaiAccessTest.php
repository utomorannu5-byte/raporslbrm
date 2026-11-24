<?php

namespace Tests\Feature\Guru;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InputNilaiAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    public function test_guru_can_access_own_class_students(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');
        $academicYear = \App\Models\AcademicYear::factory()->create(['is_active' => true]);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher)->get("/guru/input-nilai/{$student->id}");

        $response->assertOk();
        $response->assertSee($student->name);
    }

    public function test_guru_cannot_access_other_class_students(): void
    {
        $teacher1 = User::factory()->create()->assignRole('guru');
        $teacher2 = User::factory()->create()->assignRole('guru');
        $academicYear = \App\Models\AcademicYear::factory()->create(['is_active' => true]);

        $class = ClassRoom::factory()->create([
            'teacher_id' => $teacher1->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $student = Student::factory()->create(['class_id' => $class->id]);

        $response = $this->actingAs($teacher2)->get("/guru/input-nilai/{$student->id}");

        $response->assertForbidden();
    }

    public function test_guru_without_class_sees_empty_list(): void
    {
        $teacher = User::factory()->create()->assignRole('guru');

        $response = $this->actingAs($teacher)->get('/guru/input-nilai');

        $response->assertOk();
        $response->assertSee('Anda belum ditugaskan sebagai wali kelas manapun');
    }

    public function test_admin_cannot_access_guru_routes(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($admin)->get('/guru/input-nilai');

        $response->assertForbidden();
    }
}
