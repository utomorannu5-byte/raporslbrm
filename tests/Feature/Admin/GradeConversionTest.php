<?php

namespace Tests\Feature\Admin;

use App\Models\GradeConversion;
use App\Models\User;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeConversionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    public function test_seeder_creates_101_records(): void
    {
        $this->assertEquals(101, GradeConversion::count());
    }

    public function test_label_85_is_delapan_puluh_lima(): void
    {
        $conversion = GradeConversion::where('min_score', 85)->where('max_score', 85)->first();
        $this->assertNotNull($conversion);
        $this->assertEquals('Delapan puluh lima', $conversion->text_label);
    }

    public function test_label_75_is_tujuh_puluh_lima(): void
    {
        $conversion = GradeConversion::where('min_score', 75)->where('max_score', 75)->first();
        $this->assertNotNull($conversion);
        $this->assertEquals('Tujuh puluh lima', $conversion->text_label);
    }

    public function test_label_100_is_seratus(): void
    {
        $conversion = GradeConversion::where('min_score', 100)->where('max_score', 100)->first();
        $this->assertNotNull($conversion);
        $this->assertEquals('Seratus', $conversion->text_label);
    }

    public function test_label_0_is_nol(): void
    {
        $conversion = GradeConversion::where('min_score', 0)->where('max_score', 0)->first();
        $this->assertNotNull($conversion);
        $this->assertEquals('Nol', $conversion->text_label);
    }

    public function test_valid_grade_conversion_is_stored(): void
    {
        // First, delete existing 80-100 single points to allow range
        GradeConversion::whereBetween('min_score', [80, 100])->delete();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/grade-conversions', [
            'min_score' => 80,
            'max_score' => 100,
            'text_label' => 'Sangat Baik',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('grade_conversions', [
            'min_score' => 80,
            'max_score' => 100,
            'text_label' => 'Sangat Baik',
        ]);
    }

    public function test_duplicate_single_point_is_rejected(): void
    {
        // 85 already exists from seeder
        $response = $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/grade-conversions', [
            'min_score' => 85,
            'max_score' => 85,
            'text_label' => 'Duplicate',
        ]);

        $response->assertSessionHasErrors('max_score');
    }

    public function test_range_85_90_allowed_when_no_overlap(): void
    {
        // Delete existing single points 85-90 first
        GradeConversion::whereBetween('min_score', [85, 90])->delete();

        $response = $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/grade-conversions', [
            'min_score' => 85,
            'max_score' => 90,
            'text_label' => 'Sangat Baik',
        ]);

        $response->assertRedirect()->assertSessionHas('status');
        $this->assertDatabaseHas('grade_conversions', [
            'min_score' => 85,
            'max_score' => 90,
            'text_label' => 'Sangat Baik',
        ]);
    }

    public function test_overlapping_range_with_existing_range_is_rejected(): void
    {
        // Create a range 80-100
        GradeConversion::whereBetween('min_score', [80, 100])->delete();
        GradeConversion::create([
            'min_score' => 80,
            'max_score' => 100,
            'text_label' => 'Sangat Baik',
        ]);

        // Try to create overlapping range 90-95
        $response = $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/grade-conversions', [
            'min_score' => 90,
            'max_score' => 95,
            'text_label' => 'Istimewa',
        ]);

        $response->assertSessionHasErrors('max_score');
        $this->assertDatabaseMissing('grade_conversions', [
            'text_label' => 'Istimewa',
        ]);
    }

    public function test_adjacent_range_allowed(): void
    {
        // Create range 80-100
        GradeConversion::whereBetween('min_score', [80, 100])->delete();
        GradeConversion::create([
            'min_score' => 80,
            'max_score' => 100,
            'text_label' => 'Sangat Baik',
        ]);

        // Create adjacent range 75-79 (no overlap with 80-100)
        $response = $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/grade-conversions', [
            'min_score' => 75,
            'max_score' => 79,
            'text_label' => 'Baik',
        ]);

        $response->assertRedirect()->assertSessionHas('status');
        $this->assertDatabaseHas('grade_conversions', [
            'min_score' => 75,
            'max_score' => 79,
            'text_label' => 'Baik',
        ]);
    }

    public function test_admin_can_edit_grade_conversion(): void
    {
        // Create a range first
        GradeConversion::whereBetween('min_score', [80, 100])->delete();
        $conversion = GradeConversion::create([
            'min_score' => 80,
            'max_score' => 100,
            'text_label' => 'Sangat Baik',
        ]);

        $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->put("/admin/grade-conversions/{$conversion->id}", [
            'min_score' => 85,
            'max_score' => 100,
            'text_label' => 'Sangat Baik Sekali',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('grade_conversions', [
            'id' => $conversion->id,
            'min_score' => 85,
            'max_score' => 100,
            'text_label' => 'Sangat Baik Sekali',
        ]);
    }

    public function test_edit_overlapping_range_is_rejected(): void
    {
        // Create two ranges
        GradeConversion::whereBetween('min_score', [80, 100])->delete();
        $conversion1 = GradeConversion::create([
            'min_score' => 80,
            'max_score' => 100,
            'text_label' => 'Sangat Baik',
        ]);

        GradeConversion::create([
            'min_score' => 60,
            'max_score' => 79,
            'text_label' => 'Cukup',
        ]);

        // Try to edit conversion1 to overlap with conversion2
        $response = $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->put("/admin/grade-conversions/{$conversion1->id}", [
            'min_score' => 70,
            'max_score' => 85,
            'text_label' => 'Sangat Baik',
        ]);

        $response->assertSessionHasErrors('max_score');
    }

    public function test_admin_can_delete_grade_conversion(): void
    {
        $conversion = GradeConversion::create([
            'min_score' => 80,
            'max_score' => 100,
            'text_label' => 'Sangat Baik',
        ]);

        $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->delete("/admin/grade-conversions/{$conversion->id}")
            ->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseMissing('grade_conversions', ['id' => $conversion->id]);
    }

    public function test_min_score_greater_than_max_score_is_rejected(): void
    {
        $response = $this->actingAs(
            User::factory()->create(['email' => 'admin@test.test', 'password' => 'password'])
                ->assignRole('admin')
        )->post('/admin/grade-conversions', [
            'min_score' => 90,
            'max_score' => 80,
            'text_label' => 'Invalid',
        ]);

        $response->assertSessionHasErrors('max_score');
    }
}
