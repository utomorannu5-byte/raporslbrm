<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_create_student(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
            ->assignRole('admin');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            $activeYear = AcademicYear::factory()->create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);
        }

        $class = ClassRoom::factory()->create(['academic_year_id' => $activeYear->id]);

        $this->actingAs($admin)->post('/admin/students', [
            'class_id' => $class->id,
            'nisn' => '1234567890',
            'name' => 'Siswa Test',
            'parent_name' => 'Orang Tua Test',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('students', [
            'name' => 'Siswa Test',
            'nisn' => '1234567890',
            'class_id' => $class->id,
        ]);
    }

    public function test_admin_can_edit_student(): void
    {
        $admin = User::factory()->create(['email' => 'admin2@test.test', 'password' => 'password'])
            ->assignRole('admin');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            $activeYear = AcademicYear::factory()->create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);
        }

        $class1 = ClassRoom::factory()->create(['academic_year_id' => $activeYear->id]);
        $class2 = ClassRoom::factory()->create(['academic_year_id' => $activeYear->id]);

        $student = Student::factory()->create([
            'class_id' => $class1->id,
            'name' => 'Siswa Awal',
            'nisn' => '1111111111',
        ]);

        $this->actingAs($admin)->put("/admin/students/{$student->id}", [
            'class_id' => $class2->id,
            'name' => 'Siswa Diubah',
            'parent_name' => 'Orang Tua Baru',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Siswa Diubah',
            'class_id' => $class2->id,
        ]);
    }

    public function test_admin_can_delete_student(): void
    {
        $admin = User::factory()->create(['email' => 'admin3@test.test', 'password' => 'password'])
            ->assignRole('admin');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            $activeYear = AcademicYear::factory()->create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);
        }

        $class = ClassRoom::factory()->create(['academic_year_id' => $activeYear->id]);
        $student = Student::factory()->create(['class_id' => $class->id]);

        $this->actingAs($admin)->delete("/admin/students/{$student->id}")
            ->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_filter_by_class_works(): void
    {
        $admin = User::factory()->create(['email' => 'admin4@test.test', 'password' => 'password'])
            ->assignRole('admin');

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            $activeYear = AcademicYear::factory()->create(['year' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);
        }

        $class1 = ClassRoom::factory()->create(['name' => 'Kelas A', 'academic_year_id' => $activeYear->id]);
        $class2 = ClassRoom::factory()->create(['name' => 'Kelas B', 'academic_year_id' => $activeYear->id]);

        Student::factory()->create(['class_id' => $class1->id, 'name' => 'Siswa Kelas A']);
        Student::factory()->create(['class_id' => $class2->id, 'name' => 'Siswa Kelas B']);

        $response = $this->actingAs($admin)->get('/admin/students?class_id='.$class1->id);
        $response->assertOk();
        $response->assertSee('Siswa Kelas A');
        $response->assertDontSee('Siswa Kelas B');
    }
}
