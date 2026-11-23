<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradeConversionRequest;
use App\Http\Requests\UpdateGradeConversionRequest;
use App\Models\GradeConversion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GradeConversionController extends Controller
{
    /**
     * Display the grade conversion management index.
     */
    public function index(): View
    {
        $conversions = GradeConversion::orderBy('min_score')->get();

        return view('admin.grade-conversions.index', [
            'conversions' => $conversions,
        ]);
    }

    /**
     * Store a new grade conversion.
     */
    public function store(StoreGradeConversionRequest $request): RedirectResponse
    {
        GradeConversion::create($request->validated());

        return redirect()
            ->route('admin.grade-conversions.index')
            ->with('status', 'Konversi nilai berhasil ditambahkan.');
    }

    /**
     * Display the edit form for a grade conversion.
     */
    public function edit(GradeConversion $gradeConversion): View
    {
        return view('admin.grade-conversions.edit', [
            'conversion' => $gradeConversion,
        ]);
    }

    /**
     * Update a grade conversion.
     */
    public function update(UpdateGradeConversionRequest $request, GradeConversion $gradeConversion): RedirectResponse
    {
        $gradeConversion->update($request->validated());

        return redirect()
            ->route('admin.grade-conversions.index')
            ->with('status', 'Konversi nilai berhasil diperbarui.');
    }

    /**
     * Remove a grade conversion.
     */
    public function destroy(GradeConversion $gradeConversion): RedirectResponse
    {
        $gradeConversion->delete();

        return redirect()
            ->route('admin.grade-conversions.index')
            ->with('status', 'Konversi nilai berhasil dihapus.');
    }
}
