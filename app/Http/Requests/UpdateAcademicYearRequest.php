<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicYearRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $academicYear = $this->route('academic_year');

        return [
            'year' => ['sometimes', 'required', 'string', 'regex:#^\d{4}/\d{4}$#', function ($attribute, $value, $fail) use ($academicYear) {
                $query = \App\Models\AcademicYear::where('year', $value);
                if ($academicYear) {
                    $query->where('id', '!=', $academicYear->id);
                }
                if ($query->exists()) {
                    $fail('Tahun ajaran sudah terdaftar.');
                }
            }],
            'semester' => ['sometimes', 'required', 'in:Ganjil,Genap'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'year.required' => 'Tahun ajaran wajib diisi.',
            'year.regex' => 'Format tahun ajaran harus XXXX/XXXX (contoh: 2025/2026).',
            'year.unique' => 'Tahun ajaran sudah terdaftar.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester harus Ganjil atau Genap.',
            'is_active.boolean' => 'Status aktif harus true atau false.',
        ];
    }
}
