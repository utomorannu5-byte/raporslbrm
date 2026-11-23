<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolProfileRequest;
use App\Http\Requests\UpdateSchoolProfileRequest;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolProfileController extends Controller
{
    /**
     * Display the school profile form.
     */
    public function edit(): View
    {
        $schoolProfile = SchoolProfile::firstOrNew();

        return view('admin.school-profile.edit', [
            'schoolProfile' => $schoolProfile,
        ]);
    }

    /**
     * Update the school profile.
     */
    public function update(UpdateSchoolProfileRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'school-' . Str::slug($request->name) . '-' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/logos', $logoName);
            $data['logo_path'] = 'logos/' . $logoName;
        }

        $schoolProfile = SchoolProfile::firstOrCreate(
            ['id' => 1], // Always use ID 1
            $data
        );

        return redirect()
            ->route('admin.school-profile.edit')
            ->with('status', 'Profil sekolah berhasil diperbarui.');
    }
}