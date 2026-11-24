<?php

namespace Database\Seeders;

use App\Models\GradeConversion;
use Illuminate\Database\Seeder;

class GradeConversionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GradeConversion::truncate();

        for ($score = 0; $score <= 100; $score++) {
            GradeConversion::create([
                'min_score' => $score,
                'max_score' => $score,
                'text_label' => self::numberToIndonesian($score),
            ]);
        }
    }

    /**
     * Convert number to Indonesian words.
     */
    private static function numberToIndonesian(int $number): string
    {
        if ($number === 0) {
            return 'Nol';
        }

        if ($number === 100) {
            return 'Seratus';
        }

        $units = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
        $teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
        $tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];

        if ($number < 10) {
            return ucfirst($units[$number]);
        }

        if ($number < 20) {
            return ucfirst($teens[$number - 10]);
        }

        $ten = (int)($number / 10);
        $unit = $number % 10;

        if ($unit === 0) {
            return ucfirst($tens[$ten]);
        }

        return ucfirst($tens[$ten] . ' ' . $units[$unit]);
    }
}
