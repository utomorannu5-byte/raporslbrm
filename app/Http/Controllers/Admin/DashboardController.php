<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicGrade;
use App\Models\ClassRoom;
use App\Models\CompetencyGrade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the administrator dashboard with statistics.
     */
    public function index(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Global statistics
        $totalStudents = Student::count();
        $totalTeachers = User::role('guru')->count();
        $totalSubjects = Subject::count();

        $classes = collect();
        $totalClasses = 0;

        if ($activeYear) {
            $classes = ClassRoom::with(['teacher', 'academicYear'])
                ->where('academic_year_id', $activeYear->id)
                ->orderBy('name')
                ->get();

            $totalClasses = $classes->count();
        }

        // Compute completeness per class
        $classStats = $classes->map(function ($class) use ($activeYear) {
            $students = Student::where('class_id', $class->id)->get();
            $totalStudentsInClass = $students->count();

            $submittedCount = 0;
            $notSubmittedCount = 0;

            foreach ($students as $student) {
                // Check academic grades
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

            $progress = $totalStudentsInClass > 0
                ? round(($submittedCount / $totalStudentsInClass) * 100)
                : 0;

            return [
                'class' => $class,
                'total_students' => $totalStudentsInClass,
                'submitted' => $submittedCount,
                'not_submitted' => $notSubmittedCount,
                'progress' => $progress,
            ];
        });

        return view('admin.dashboard', [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'totalSubjects' => $totalSubjects,
            'activeYear' => $activeYear,
            'classStats' => $classStats,
        ]);
    }
}