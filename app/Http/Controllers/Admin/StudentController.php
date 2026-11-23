<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display the student management index.
     */
    public function index(): View
    {
        $classes = ClassRoom::with('academicYear')
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        $selectedClassId = request('class_id');
        $students = Student::with('classRoom.academicYear')
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->latest()
            ->get();

        return view('admin.students.index', [
            'students' => $students,
            'classes' => $classes,
            'selectedClassId' => $selectedClassId,
        ]);
    }

    /**
     * Store a new student.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        Student::create($request->validated());

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Display the edit form for a student.
     */
    public function edit(Student $student): View
    {
        $classes = ClassRoom::with('academicYear')
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        return view('admin.students.edit', [
            'student' => $student,
            'classes' => $classes,
        ]);
    }

    /**
     * Update a student.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Siswa berhasil diperbarui.');
    }

    /**
     * Remove a student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Siswa berhasil dihapus.');
    }
}
