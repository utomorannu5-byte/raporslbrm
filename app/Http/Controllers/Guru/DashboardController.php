<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\CompetencyGrade;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard with statistics.
     */
    public function index(): View
    {
        $teacher = auth()->user();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $classes = collect();
        $hasClass = false;

        if ($activeYear) {
            $classes = ClassRoom::with('academicYear')
                ->where('teacher_id', $teacher->id)
                ->where('academic_year_id', $activeYear->id)
                ->orderBy('name')
                ->get();

            $hasClass = $classes->isNotEmpty();
        }

        // Compute statistics per class
        $classStats = $classes->map(function ($class) use ($activeYear) {
            $students = Student::where('class_id', $class->id)->get();
            $totalStudents = $students->count();

            $submittedCount = 0;
            $notSubmittedCount = 0;

            foreach ($students as $student) {
                $academicGrades = AcademicGrade::where('student_id', $student->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->get();

                $competencyGrades = CompetencyGrade::where('student_id', $student->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->get();

                $allAcademicSubmitted = $academicGrades->isNotEmpty()
                    && $academicGrades->every(fn ($g) => $g->is_submitted);
                $allCompetencySubmitted = $competencyGrades->isNotEmpty()
                    && $competencyGrades->every(fn ($g) => $g->is_submitted);

                if ($allAcademicSubmitted && $allCompetencySubmitted) {
                    $submittedCount++;
                } else {
                    $notSubmittedCount++;
                }
            }

            $progress = $totalStudents > 0
                ? round(($submittedCount / $totalStudents) * 100)
                : 0;

            return [
                'class' => $class,
                'total_students' => $totalStudents,
                'submitted' => $submittedCount,
                'not_submitted' => $notSubmittedCount,
                'progress' => $progress,
            ];
        });

        // Aggregate stats
        $totalStudents = $classStats->sum('total_students');
        $totalSubmitted = $classStats->sum('submitted');
        $totalNotSubmitted = $classStats->sum('not_submitted');

        return view('guru.dashboard', [
            'teacher' => $teacher,
            'activeYear' => $activeYear,
            'classStats' => $classStats,
            'totalStudents' => $totalStudents,
            'totalSubmitted' => $totalSubmitted,
            'totalNotSubmitted' => $totalNotSubmitted,
            'hasClass' => $hasClass,
        ]);
    }
}