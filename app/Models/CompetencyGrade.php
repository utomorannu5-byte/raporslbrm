<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'indicator_id',
        'value',
        'is_submitted',
    ];

    protected $casts = [
        'value' => 'string',
        'is_submitted' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function indicator()
    {
        return $this->belongsTo(CompetencyIndicator::class, 'indicator_id');
    }
}