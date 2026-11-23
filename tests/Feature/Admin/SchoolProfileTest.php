<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_update_school_profile(): void
    {
        $this->actingAs(
            User::factory()->create(['email' => 'admin@erapor-slb.test', 'password' => 'password'])
                ->assignRole('admin')
        )->put('/admin/school-profile', [
            'name' => 'SEKOLAH LUAR BIASA RUMAH MELATI',
            'address' => 'Jl. Contoh No. 1',
            'phone' => '0812345678',
            'email' => 'contact@rumahmelati.test',
        ])->assertRedirect()->assertSessionHas('status');
    }
}
