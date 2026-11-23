<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttitude extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'sikap',
        'kerajinan',
        'kebersihan',
        'sick',
        'permission',
        'absent',
    ];

    protected $casts = [
        'sikap' => 'string',
        'kerajinan' => 'string',
        'kebersihan' => 'string',
        'sick' => 'integer',
        'permission' => 'integer',
        'absent' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}