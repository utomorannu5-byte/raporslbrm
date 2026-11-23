<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRoomRequest;
use App\Http\Requests\UpdateClassRoomRequest;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\CompetencyDomain;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassRoomController extends Controller
{
    /**
     * Display the class management index.
     */
    public function index(): View
    {
        $classes = ClassRoom::with([
            'academicYear',
            'teacher',
            'students',
            'subjects',
            'competencyIndicators',
        ])->latest()->get();

        return view('admin.classes.index', [
            'classes' => $classes,
        ]);
    }

    /**
     * Store a new class.
     */
    public function store(StoreClassRoomRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $classRoom = ClassRoom::create([
                'name' => $request->validated()['name'],
                'academic_year_id' => $request->validated()['academic_year_id'],
                'teacher_id' => $request->validated()['teacher_id'] ?? null,
            ]);

            if ($request->filled('subject_ids')) {
                $classRoom->subjects()->sync($request->validated()['subject_ids']);
            }

            if ($request->filled('indicator_ids')) {
                $classRoom->competencyIndicators()->sync($request->validated()['indicator_ids']);
            }
        });

        return redirect()
            ->route('admin.classes.index')
            ->with('status', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the edit form for a class.
     */
    public function edit(ClassRoom $class): View
    {
        $class->load(['subjects', 'competencyIndicators.domain']);

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $teachers = User::role('guru')->get();
        $subjects = Subject::orderBy('name')->get();
        $domains = CompetencyDomain::with('indicators')->orderBy('sort_order')->get();

        return view('admin.classes.edit', [
            'class' => $class,
            'academicYears' => $academicYears,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'domains' => $domains,
        ]);
    }

    /**
     * Update a class.
     */
    public function update(UpdateClassRoomRequest $request, ClassRoom $class): RedirectResponse
    {
        DB::transaction(function () use ($request, $class) {
            $class->update([
                'name' => $request->validated()['name'],
                'academic_year_id' => $request->validated()['academic_year_id'],
                'teacher_id' => $request->validated()['teacher_id'] ?? null,
            ]);

            $class->subjects()->sync($request->validated()['subject_ids'] ?? []);
            $class->competencyIndicators()->sync($request->validated()['indicator_ids'] ?? []);
        });

        return redirect()
            ->route('admin.classes.index')
            ->with('status', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove a class.
     */
    public function destroy(ClassRoom $class): RedirectResponse
    {
        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('status', 'Kelas berhasil dihapus.');
    }
}
