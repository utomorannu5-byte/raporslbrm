<?php

namespace Tests\Feature\Admin;

use App\Models\Subject;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_create_subject(): void
    {
        $this->actingAs(
            User::factory()->create(['email' => 'admin@erapor-slb.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/subjects', [
            'name' => 'Matematika',
            'default_kkm' => 75,
        ])->assertRedirect()->assertSessionHas('status');
    }

    public function test_admin_can_edit_subject(): void
    {
        $subject = Subject::create([
            'name' => 'Matematika Dasar',
            'default_kkm' => 70,
        ]);

        $this->actingAs(
            User::factory()->create(['email' => 'admin@erapor-slb.test', 'password' => 'password'])
                ->assignRole('admin')
        )->put("/admin/subjects/{$subject->id}", [
            'name' => 'Matematika Lanjutan',
            'default_kkm' => 80,
        ])->assertRedirect()->assertSessionHas('status');
    }

    public function test_admin_can_delete_subject(): void
    {
        $subject = Subject::create([
            'name' => 'Sejarah',
            'default_kkm' => 65,
        ]);

        $this->actingAs(
            User::factory()->create(['email' => 'admin@erapor-slb.test', 'password' => 'password'])
                ->assignRole('admin')
        )->delete("/admin/subjects/{$subject->id}")->assertRedirect()->assertSessionHas('status');
    }
}