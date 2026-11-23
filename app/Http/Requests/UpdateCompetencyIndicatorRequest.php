<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetencyIndicatorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'domain_id' => ['sometimes', 'required', 'exists:competency_domains,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
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
            'domain_id.required' => 'Domain kompetensi wajib dipilih.',
            'domain_id.exists' => 'Domain kompetensi tidak valid.',
            'name.required' => 'Nama indikator wajib diisi.',
            'name.max' => 'Nama indikator maksimal 255 karakter.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
        ];
    }
}
