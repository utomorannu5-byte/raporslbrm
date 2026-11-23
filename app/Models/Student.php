<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'nisn',
        'name',
        'parent_name',
    ];

    protected $casts = [
        'nisn' => 'string',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function academicGrades()
    {
        return $this->hasMany(AcademicGrade::class, 'student_id');
    }

    public function studentAttitude()
    {
        return $this->hasOne(StudentAttitude::class, 'student_id');
    }

    public function competencyGrades()
    {
        return $this->hasMany(CompetencyGrade::class, 'student_id');
    }

    public function reportArchives()
    {
        return $this->hasMany(ReportArchive::class, 'student_id');
    }
}
