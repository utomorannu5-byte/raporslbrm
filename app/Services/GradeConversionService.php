<?php

namespace App\Services;

use App\Models\GradeConversion;

class GradeConversionService
{
    /**
     * Convert a numeric score to its Indonesian text label.
     *
     * @param int $score
     * @return string
     */
    public function numberToText(int $score): string
    {
        $conversion = GradeConversion::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        return $conversion?->text_label ?? (string) $score;
    }
}
