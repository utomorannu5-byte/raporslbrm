<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompetencyDomainRequest;
use App\Http\Requests\UpdateCompetencyDomainRequest;
use App\Models\CompetencyDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompetencyDomainController extends Controller
{
    /**
     * Display the competency domain management index.
     */
    public function index(): View
    {
        $domains = CompetencyDomain::withCount('indicators')
            ->orderBy('sort_order')
            ->get();

        return view('admin.competency-domains.index', [
            'domains' => $domains,
        ]);
    }

    /**
     * Store a new competency domain.
     */
    public function store(StoreCompetencyDomainRequest $request): RedirectResponse
    {
        CompetencyDomain::create($request->validated());

        return redirect()
            ->route('admin.competency-domains.index')
            ->with('status', 'Domain kompetensi berhasil ditambahkan.');
    }

    /**
     * Display the edit form for a competency domain.
     */
    public function edit(CompetencyDomain $competencyDomain): View
    {
        return view('admin.competency-domains.edit', [
            'domain' => $competencyDomain,
        ]);
    }

    /**
     * Update a competency domain.
     */
    public function update(UpdateCompetencyDomainRequest $request, CompetencyDomain $competencyDomain): RedirectResponse
    {
        $competencyDomain->update($request->validated());

        return redirect()
            ->route('admin.competency-domains.index')
            ->with('status', 'Domain kompetensi berhasil diperbarui.');
    }

    /**
     * Remove a competency domain.
     */
    public function destroy(CompetencyDomain $competencyDomain): RedirectResponse
    {
        $competencyDomain->delete();

        return redirect()
            ->route('admin.competency-domains.index')
            ->with('status', 'Domain kompetensi berhasil dihapus.');
    }
}
