<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectController extends Controller
{
    /**
     * Display the subject management index.
     */
    public function index(): View
    {
        $subjects = Subject::latest()->get();

        return view('admin.subjects.index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store a new subject.
     */
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create([
            'name' => $request->validated()['name'],
            'default_kkm' => $request->validated()['default_kkm'],
        ]);

        return redirect()
            ->route('admin.subjects.index')
            ->with('status', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the edit form for a subject.
     */
    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update a subject.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validated();

        $subject->update([
            'name' => $validated['name'],
            'default_kkm' => $validated['default_kkm'],
        ]);

        return redirect()
            ->route('admin.subjects.index')
            ->with('status', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove a subject.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('status', 'Mata pelajaran berhasil dihapus.');
    }
}