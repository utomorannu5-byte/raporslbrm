<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreInputNilaiRequest;
use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\CompetencyGrade;
use App\Models\CompetencyIndicator;
use App\Models\Student;
use App\Models\StudentAttitude;
use App\Models\Subject;
use App\Services\GradeConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

                $compGrades = \App\Models\CompetencyGrade::where('student_id', $student->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->get();

                $totalSubjects = $student->classRoom->subjects->count();
                $totalIndicators = $student->classRoom->competencyIndicators->count();
                
                $submittedAcademic = $grades->where('is_submitted', true)->count();
                $submittedCompetency = $compGrades->where('is_submitted', true)->count();
                $filledAcademic = $grades->where('score_number', '!=', null)->count();
                $filledCompetency = $compGrades->where('value', '!=', null)->count();
                $totalSubjects = $student->classRoom->subjects->count();
                $totalIndicators = $student->classRoom->competencyIndicators->count();

                $allSubmitted = ($submittedAcademic === $totalSubjects && $totalSubjects > 0)
                    && ($submittedCompetency === $totalIndicators && $totalIndicators > 0);
                $anyFilled = $filledAcademic > 0 || $filledCompetency > 0;

                if ($allSubmitted) {
                    $status = 'Terkunci';
                } elseif ($anyFilled) {
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

        // Get competency indicators assigned to this student's class, grouped by domain
        $indicators = $student->classRoom->competencyIndicators()
            ->with('domain')
            ->orderBy('domain_id')
            ->orderBy('sort_order')
            ->get();

        $indicatorsByDomain = $indicators->groupBy('domain_id')->map(function ($items, $domainId) {
            return [
                'domain' => $items->first()->domain,
                'indicators' => $items,
            ];
        });

        // Get or create competency grades for each indicator
        $competencyGrades = $indicators->map(function ($indicator) use ($student, $academicYear) {
            return CompetencyGrade::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'indicator_id' => $indicator->id,
                ],
                [
                    'value' => null,
                    'is_submitted' => false,
                ]
            );
        })->keyBy('indicator_id');

        // Get or create academic grades for each subject
        $subjects = $student->classRoom->subjects()->orderBy('name')->get();
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

        // Check if all grades are submitted (locked) - use actual competency grades count
        $totalSubjects = $subjects->count();
        $totalCompetencyGrades = $competencyGrades->count();
        
        $academicGrades = AcademicGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();
        $competencyGrades = CompetencyGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();

        $submittedAcademic = $academicGrades->where('is_submitted', true)->count();
        $submittedCompetency = $competencyGrades->where('is_submitted', true)->count();

        $isLocked = ($submittedAcademic === $totalSubjects && $totalSubjects > 0)
            && ($submittedCompetency === $totalCompetencyGrades && $totalCompetencyGrades > 0);

        return view('guru.input-nilai.edit', [
            'student' => $student,
            'academicYear' => $academicYear,
            'subjects' => $subjects,
            'grades' => $grades,
            'attitudes' => $attitudes,
            'isLocked' => $isLocked,
            'indicatorsByDomain' => $indicatorsByDomain,
            'competencyGrades' => $competencyGrades,
        ]);
    }

    /**
     * Update the specified student's grades (draft).
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

        // Check if locked - use actual competency grades count
        $totalSubjects = $student->classRoom->subjects->count();
        $totalCompetencyGrades = CompetencyGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->count();
        
        $academicGrades = AcademicGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();
        $competencyGrades = CompetencyGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();

        $submittedAcademic = $academicGrades->where('is_submitted', true)->count();
        $submittedCompetency = $competencyGrades->where('is_submitted', true)->count();

        $isLocked = ($submittedAcademic === $totalSubjects && $totalSubjects > 0)
            && ($submittedCompetency === $totalCompetencyGrades && $totalCompetencyGrades > 0);

        if ($isLocked) {
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

        // Ensure all competency indicators have competency_grades records (create if not exists)
        $classIndicators = $student->classRoom->competencyIndicators;
        foreach ($classIndicators as $indicator) {
            CompetencyGrade::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'indicator_id' => $indicator->id,
                ],
                [
                    'value' => null,
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

        // Update competency grades from request
        if (isset($validated['competencies'])) {
            foreach ($validated['competencies'] as $indicatorId => $value) {
                // Ensure indicator belongs to student's class
                if (!$classIndicators->contains($indicatorId)) {
                    continue;
                }

                $compGrade = CompetencyGrade::where('student_id', $student->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('indicator_id', $indicatorId)
                    ->first();

                if ($compGrade && !$compGrade->is_submitted) {
                    $compGrade->value = $value;
                    $compGrade->save();
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
     * Submit & lock grades.
     */
    public function submit(Student $student): RedirectResponse
    {
        // Authorization: only the class teacher can access
        if ($student->classRoom->teacher_id !== auth()->id()) {
            abort(403, 'Anda tidak berwenang mengakses siswa ini.');
        }

        $academicYear = AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Check if already locked - use actual competency grades count
        $totalSubjects = $student->classRoom->subjects->count();
        $totalCompetencyGrades = CompetencyGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->count();
        
        $academicGrades = AcademicGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();
        $competencyGrades = CompetencyGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();

        $submittedAcademic = $academicGrades->where('is_submitted', true)->count();
        $submittedCompetency = $competencyGrades->where('is_submitted', true)->count();

        $isLocked = ($submittedAcademic === $totalSubjects && $totalSubjects > 0)
            && ($submittedCompetency === $totalCompetencyGrades && $totalCompetencyGrades > 0);

        if ($isLocked) {
            return back()->with('error', 'Nilai sudah terkunci, tidak dapat diubah.');
        }

        // VALIDATION: Check completeness
        $errors = [];

        // Check academic grades completeness
        $missingAcademic = $academicGrades->where('score_number', null)->count();
        if ($missingAcademic > 0) {
            $errors[] = "Masih ada {$missingAcademic} nilai akademik yang kosong. Lengkapi dulu.";
        }

        // Check attitudes completeness
        $attitudes = StudentAttitude::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->first();
        
        if (!$attitudes || !$attitudes->sikap || !$attitudes->kerajinan || !$attitudes->kebersihan) {
            $errors[] = 'Kepribadian (Sikap, Kerajinan, Kebersihan) belum lengkap. Lengkapi dulu.';
        }

        // Check competency grades completeness
        $missingCompetency = $competencyGrades->where('value', null)->count();
        if ($missingCompetency > 0) {
            $errors[] = "Masih ada {$missingCompetency} indikator kompetensi yang belum dinilai. Lengkapi dulu.";
        }

        if (!empty($errors)) {
            return back()->with('error', implode(' ', $errors));
        }

        // All valid - lock in transaction
        DB::transaction(function () use ($student, $academicYear) {
            // Lock academic grades
            AcademicGrade::where('student_id', $student->id)
                ->where('academic_year_id', $academicYear->id)
                ->update(['is_submitted' => true]);

            // Lock competency grades
            CompetencyGrade::where('student_id', $student->id)
                ->where('academic_year_id', $academicYear->id)
                ->update(['is_submitted' => true]);
        });

        return back()->with('status', 'Nilai berhasil dikunci. Rapor siap dicetak.');
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