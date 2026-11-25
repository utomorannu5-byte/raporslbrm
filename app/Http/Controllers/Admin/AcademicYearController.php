<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    /**
     * Display the academic year management index.
     */
    public function index(): View
    {
        $academicYears = AcademicYear::latest()->get();

        return view('admin.academic-years.index', [
            'academicYears' => $academicYears,
        ]);
    }

    /**
     * Store a new academic year.
     */
    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            // Set all existing years to inactive
            AcademicYear::query()->update(['is_active' => false]);

            // Create new year as active
            AcademicYear::create([
                'year' => $request->validated()['year'],
                'semester' => $request->validated()['semester'],
                'is_active' => true,
            ]);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('status', 'Tahun ajaran berhasil diset aktivkan.');
    }

    /**
     * Show the form for creating a new academic year.
     */
    public function create(): View
    {
        return view('admin.academic-years.create');
    }

    /**
     * Display the edit form for an academic year.
     */
    public function edit(AcademicYear $academicYear): View
    {
        return view('admin.academic-years.edit', [
            'academicYear' => $academicYear,
        ]);
    }

    /**
     * Update an academic year.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validated();

        $academicYear->update([
            'year' => $validated['year'],
            'semester' => $validated['semester'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        // If this year is set as active, deactivate all others
        if ($validated['is_active'] ?? false) {
            AcademicYear::where('id', '!=', $academicYear->id)
                ->update(['is_active' => false]);
        }

        return redirect()
            ->route('admin.academic-years.index')
            ->with('status', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Toggle active status for an academic year.
     */
    public function toggle(AcademicYear $academicYear): RedirectResponse
    {
        DB::transaction(function () use ($academicYear) {
            // Set all years to inactive
            AcademicYear::query()->update(['is_active' => false]);

            // Set the selected year as active
            $academicYear->update(['is_active' => true]);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('status', 'Tahun ajaran berhasil di-set sebagai aktif.');
    }
}
