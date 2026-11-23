<?php

namespace Tests\Feature\Admin;

use App\Models\CompetencyDomain;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetencyDomainCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_create_competency_domain(): void
    {
        $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/competency-domains', [
            'name' => 'Kognitif',
            'sort_order' => 1,
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('competency_domains', [
            'name' => 'Kognitif',
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_edit_competency_domain(): void
    {
        $domain = CompetencyDomain::factory()->create(['name' => 'Awal', 'sort_order' => 1]);

        $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->put("/admin/competency-domains/{$domain->id}", [
            'name' => 'Diubah',
            'sort_order' => 2,
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('competency_domains', [
            'id' => $domain->id,
            'name' => 'Diubah',
            'sort_order' => 2,
        ]);
    }

    public function test_admin_can_delete_competency_domain(): void
    {
        $domain = CompetencyDomain::factory()->create(['name' => 'Akan Dihapus']);

        $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->delete("/admin/competency-domains/{$domain->id}")
            ->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseMissing('competency_domains', ['id' => $domain->id]);
    }
}
