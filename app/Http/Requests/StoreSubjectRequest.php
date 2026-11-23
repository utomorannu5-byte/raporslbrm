<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            'default_kkm' => ['required', 'integer', 'min:0', 'max:100'],
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
            'name.required' => 'Nama mapel wajib diisi.',
            'name.max' => 'Nama mapel maksimal 255 karakter.',
            'default_kkm.required' => 'KKM default wajib diisi.',
            'default_kkm.integer' => 'KKM default harus berupa angka.',
            'default_kkm.min' => 'KKM minimal 0.',
            'default_kkm.max' => 'KKM maksimal 100.',
        ];
    }
}
