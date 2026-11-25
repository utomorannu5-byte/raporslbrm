<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreInputNilaiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grades' => ['sometimes', 'array'],
            'grades.*.score_number' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sikap' => ['sometimes', 'in:A,B,C,D'],
            'kerajinan' => ['sometimes', 'in:A,B,C,D'],
            'kebersihan' => ['sometimes', 'in:A,B,C,D'],
            'sick' => ['sometimes', 'integer', 'min:0'],
            'permission' => ['sometimes', 'integer', 'min:0'],
            'absent' => ['sometimes', 'integer', 'min:0'],
            'competencies' => ['sometimes', 'array'],
            'competencies.*' => ['nullable', 'in:M,AV,AF,AP'],
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
            'grades.*.score_number.integer' => 'Nilai angka harus berupa bilangan bulat.',
            'grades.*.score_number.min' => 'Nilai angka minimal 0.',
            'grades.*.score_number.max' => 'Nilai angka maksimal 100.',
            'sikap.in' => 'Sikap harus A, B, C, atau D.',
            'kerajinan.in' => 'Kerajinan harus A, B, C, atau D.',
            'kebersihan.in' => 'Kebersihan harus A, B, C, atau D.',
            'sick.integer' => 'Sakit harus berupa bilangan bulat.',
            'sick.min' => 'Sakit minimal 0.',
            'permission.integer' => 'Izin harus berupa bilangan bulat.',
            'permission.min' => 'Izin minimal 0.',
            'absent.integer' => 'Tanpa keterangan harus berupa bilangan bulat.',
            'absent.min' => 'Tanpa keterangan minimal 0.',
        ];
    }
}
