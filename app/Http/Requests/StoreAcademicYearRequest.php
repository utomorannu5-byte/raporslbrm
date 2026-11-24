<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'year' => ['required', 'string', 'regex:#^\d{4}/\d{4}$#', 'unique:academic_years,year'],
            'semester' => ['required', 'in:Ganjil,Genap'],
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
