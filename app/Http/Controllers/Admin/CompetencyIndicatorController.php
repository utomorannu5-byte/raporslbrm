<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompetencyIndicatorRequest;
use App\Http\Requests\UpdateCompetencyIndicatorRequest;
use App\Models\CompetencyDomain;
use App\Models\CompetencyIndicator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompetencyIndicatorController extends Controller
{
    /**
     * Display the competency indicator management index.
     */
    public function index(): View
    {
        $domains = CompetencyDomain::with([
            'indicators' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])->orderBy('sort_order')->get();

        return view('admin.competency-indicators.index', [
            'domains' => $domains,
        ]);
    }

    /**
     * Store a new competency indicator.
     */
    public function store(StoreCompetencyIndicatorRequest $request): RedirectResponse
    {
        CompetencyIndicator::create($request->validated());

        return redirect()
            ->route('admin.competency-indicators.index')
            ->with('status', 'Indikator kompetensi berhasil ditambahkan.');
    }

    /**
     * Display the edit form for a competency indicator.
     */
    public function edit(CompetencyIndicator $competencyIndicator): View
    {
        $domains = CompetencyDomain::orderBy('sort_order')->get();

        return view('admin.competency-indicators.edit', [
            'indicator' => $competencyIndicator,
            'domains' => $domains,
        ]);
    }

    /**
     * Update a competency indicator.
     */
    public function update(UpdateCompetencyIndicatorRequest $request, CompetencyIndicator $competencyIndicator): RedirectResponse
    {
        $competencyIndicator->update($request->validated());

        return redirect()
            ->route('admin.competency-indicators.index')
            ->with('status', 'Indikator kompetensi berhasil diperbarui.');
    }

    /**
     * Remove a competency indicator.
     */
    public function destroy(CompetencyIndicator $competencyIndicator): RedirectResponse
    {
        $competencyIndicator->delete();

        return redirect()
            ->route('admin.competency-indicators.index')
            ->with('status', 'Indikator kompetensi berhasil dihapus.');
    }
}
