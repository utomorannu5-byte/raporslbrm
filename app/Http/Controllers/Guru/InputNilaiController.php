<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreInputNilaiRequest;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentAttitude;
use App\Models\Subject;
use App\Services\GradeConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InputNilaiController extends Controller
{
    public function __construct(
        protected GradeConversionService $gradeConversionService
    ) {}

    /**
     * Display the list of students for the logged-in teacher.
     */
    public function index(): View
    {
        $teacher = auth()->user();
        $academicYear = AcademicYear::where('is_active', true)->first();

        $students = Student::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->with('classRoom')->get();

        $studentsWithStatus = $students->map(function ($student) use ($academicYear) {
            if ($academicYear) {
                $grades = \App\Models\AcademicGrade::where('student_id', $student->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->get();

                $totalSubjects = $student->classRoom->subjects->count();
                $submittedCount = $grades->where('is_submitted', true)->count();
                $filledCount = $grades->where('score_number', '!=', null)->count();

                if ($submittedCount === $totalSubjects && $totalSubjects > 0) {
                    $status = 'Terkunci';
                } elseif ($filledCount > 0) {
                    $status = 'Belum Lengkap';
                } else {
                    $status = 'Belum Diisi';
                }
            } else {
                $status = 'Tahun Ajaran Belum Aktif';
            }

            return [
                'student' => $student,
                'status' => $status,
            ];
        });

        return view('guru.input-nilai.index', [
            'studentsWithStatus' => $studentsWithStatus,
            'academicYear' => $academicYear,
        ]);
    }

    /**
     * Show the form for editing the specified student's grades.
     */
    public function edit(Student $student): View
    {
        // Authorization: only the class teacher can access
        if ($student->classRoom->teacher_id !== auth()->id()) {
            abort(403, 'Anda tidak berwenang mengakses siswa ini.');
        }

        $academicYear = AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            abort(404, 'Tidak ada tahun ajaran aktif.');
        }

        // Get subjects assigned to this student's class
        $subjects = $student->classRoom->subjects()->orderBy('name')->get();

        // Get or create academic grades for each subject
        $grades = $subjects->map(function ($subject) use ($student, $academicYear) {
            return AcademicGrade::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'kkm' => $subject->default_kkm,
                    'score_number' => null,
                    'score_text' => null,
                    'is_submitted' => false,
                ]
            );
        });

        // Get or create student attitudes with defaults
        $attitudes = StudentAttitude::firstOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'sikap' => null,
                'kerajinan' => null,
                'kebersihan' => null,
                'sick' => 0,
                'permission' => 0,
                'absent' => 0,
            ]
        );

        // Check if all grades are submitted (locked)
        $isLocked = $grades->every(fn($g) => $g->is_submitted) && $grades->count() > 0;

        return view('guru.input-nilai.edit', [
            'student' => $student,
            'academicYear' => $academicYear,
            'subjects' => $subjects,
            'grades' => $grades,
            'attitudes' => $attitudes,
            'isLocked' => $isLocked,
        ]);
    }

    /**
     * Update the specified student's grades.
     */
    public function update(StoreInputNilaiRequest $request, Student $student): RedirectResponse
    {
        // Authorization: only the class teacher can access
        if ($student->classRoom->teacher_id !== auth()->id()) {
            abort(403, 'Anda tidak berwenang mengakses siswa ini.');
        }

        $academicYear = AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Check if locked
        $grades = AcademicGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();

        if ($grades->every(fn($g) => $g->is_submitted) && $grades->count() > 0) {
            return back()->with('error', 'Nilai sudah terkunci, tidak dapat diubah.');
        }

        $validated = $request->validated();

        // Get subjects for this student's class
        $classSubjects = $student->classRoom->subjects;

        // Ensure all subjects have academic_grades records (create if not exists)
        foreach ($classSubjects as $subject) {
            AcademicGrade::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'kkm' => $subject->default_kkm,
                    'score_number' => null,
                    'score_text' => null,
                    'is_submitted' => false,
                ]
            );
        }

        // Update academic grades from request
        if (isset($validated['grades'])) {
            foreach ($validated['grades'] as $subjectId => $gradeData) {
                // Ensure subject belongs to student's class
                if (!$classSubjects->contains($subjectId)) {
                    continue;
                }

                $grade = AcademicGrade::where('student_id', $student->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('subject_id', $subjectId)
                    ->first();

                if ($grade && !$grade->is_submitted) {
                    $scoreNumber = $gradeData['score_number'] ?? null;
                    $grade->score_number = $scoreNumber;
                    $grade->score_text = $scoreNumber !== null
                        ? $this->gradeConversionService->numberToText($scoreNumber)
                        : null;
                    $grade->save();
                }
            }
        }

        // Update student attitudes - only update fields that are present in request
        $attitudes = StudentAttitude::firstOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'sikap' => null,
                'kerajinan' => null,
                'kebersihan' => null,
                'sick' => 0,
                'permission' => 0,
                'absent' => 0,
            ]
        );

        // Only fill fields that are present in validated data
        $attitudeFields = ['sikap', 'kerajinan', 'kebersihan', 'sick', 'permission', 'absent'];
        foreach ($attitudeFields as $field) {
            if (array_key_exists($field, $validated)) {
                $attitudes->$field = $validated[$field];
            }
        }
        $attitudes->save();

        return back()->with('status', 'Draft berhasil disimpan.');
    }

    /**
     * Convert a numeric score to text (AJAX endpoint).
     */
    public function convertGrade(int $score): JsonResponse
    {
        return response()->json([
            'text' => $this->gradeConversionService->numberToText($score),
        ]);
    }
}
