<?php

namespace Tests\Feature\Guru;

use App\Models\GradeConversion;
use App\Services\GradeConversionService;
use Database\Seeders\GradeConversionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeConversionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(GradeConversionSeeder::class);
    }

    public function test_zero_returns_nol(): void
    {
        $service = new GradeConversionService();
        $this->assertEquals('Nol', $service->numberToText(0));
    }

    public function test_seventy_five_returns_tujuh_puluh_lima(): void
    {
        $service = new GradeConversionService();
        $this->assertEquals('Tujuh puluh lima', $service->numberToText(75));
    }

    public function test_eighty_five_returns_delapan_puluh_lima(): void
    {
        $service = new GradeConversionService();
        $this->assertEquals('Delapan puluh lima', $service->numberToText(85));
    }

    public function test_one_hundred_returns_seratus(): void
    {
        $service = new GradeConversionService();
        $this->assertEquals('Seratus', $service->numberToText(100));
    }

    public function test_unknown_score_returns_score_as_string(): void
    {
        // Delete all conversions to test fallback
        GradeConversion::truncate();

        $service = new GradeConversionService();
        $this->assertEquals('50', $service->numberToText(50));
    }
}
