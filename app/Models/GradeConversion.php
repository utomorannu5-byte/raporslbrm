<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_score',
        'max_score',
        'text_label',
    ];

    protected $casts = [
        'min_score' => 'integer',
        'max_score' => 'integer',
    ];

    public function scopeByScore($query, $score)
    {
        return $query->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }
}
