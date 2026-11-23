<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRoomRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'subject_ids' => ['sometimes', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
            'indicator_ids' => ['sometimes', 'array'],
            'indicator_ids.*' => ['exists:competency_indicators,id'],
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
            'name.required' => 'Nama kelas wajib diisi.',
            'name.max' => 'Nama kelas maksimal 255 karakter.',
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',
            'academic_year_id.exists' => 'Tahun ajaran tidak valid.',
            'teacher_id.exists' => 'Guru tidak valid.',
            'subject_ids.array' => 'Format mapel tidak valid.',
            'subject_ids.*.exists' => 'Mapel tidak valid.',
            'indicator_ids.array' => 'Format indikator tidak valid.',
            'indicator_ids.*.exists' => 'Indikator tidak valid.',
        ];
    }
}
