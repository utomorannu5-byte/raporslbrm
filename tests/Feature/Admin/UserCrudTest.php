<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs(
            User::factory()->create(['email' => 'admin@erapor-slb.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/users', [
            'name' => 'Guru Baru',
            'email' => 'guru.baru@erapor-slb.test',
            'password' => 'password123',
            'role' => 'guru',
        ])->assertRedirect()->assertSessionHas('status');
    }

    public function test_admin_can_edit_user(): void
    {
        $user = User::create([
            'name' => 'Guru Awal',
            'email' => 'guru.awal@erapor-slb.test',
            'password' => bcrypt('password123'),
        ]);
        $user->assignRole('guru');

        $this->actingAs(
            User::factory()->create(['email' => 'admin@erapor-slb.test', 'password' => 'password'])
                ->assignRole('admin')
        )->put("/admin/users/{$user->id}", [
            'name' => 'Guru Diubah',
            'email' => 'guru.diubah@erapor-slb.test',
            'role' => 'admin',
        ])->assertRedirect()->assertSessionHas('status');
    }

    public function test_guru_cannot_access_users_route(): void
    {
        $this->actingAs(
            User::factory()->create(['email' => 'guru@erapor-slb.test', 'password' => 'password'])
                ->assignRole('guru')
        )->get('/admin/users')->assertForbidden();
    }

    public function test_user_cannot_delete_own_account(): void
    {
        $user = User::create([
            'name' => 'Guru Sendiri',
            'email' => 'sendiri@erapor-slb.test',
            'password' => bcrypt('password123'),
        ]);
        $user->assignRole('guru');

        $this->actingAs($user)->delete('/admin/users/'.$user->id)
            ->assertForbidden();
    }
}
