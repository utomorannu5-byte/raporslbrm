<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use App\Services\AcademicYearCopyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

                    IlluminateSupportFacadesog::info("copy success", ["result" => $result]);
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

                    IlluminateSupportFacadesog::info("copy success", ["result" => $result]);
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

                    IlluminateSupportFacadesog::info("copy success", ["result" => $result]);
            return redirect()
            ->route('admin.academic-years.index')
            ->with('status', 'Tahun ajaran berhasil di-set sebagai aktif.');
    }

    /**
     * Show form to copy data from one academic year to another.
     */
    public function copyForm(): View
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();

        return view('admin.academic-years.copy', compact('academicYears'));
    }

    /**
     * Copy master data from one academic year to another.
     */
    public function copy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_year_id' => ['required', 'exists:academic_years,id'],
            'to_year_id' => ['required', 'exists:academic_years,id', 'different:from_year_id'],
        ]);

        $from = AcademicYear::findOrFail($validated['from_year_id']);
        $to = AcademicYear::findOrFail($validated['to_year_id']);

        $service = new AcademicYearCopyService();

        try {
            $result = $service->copy($from, $to);

            return redirect()
                ->route('admin.academic-years.index')
                ->with('status', "Berhasil copy {$result['classes_copied']} kelas, {$result['students_copied']} siswa dari {$from->year} {$from->semester} ke {$to->year} {$to->semester}. Nilai tidak di-copy (kosong).");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal copy data: ' . $e->getMessage());
        }
    }
}
