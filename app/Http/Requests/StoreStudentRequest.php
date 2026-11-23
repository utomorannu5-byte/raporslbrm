<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn'],
            'name' => ['required', 'string', 'max:255'],
            'parent_name' => ['required', 'string', 'max:255'],
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
            'class_id.required' => 'Kelas wajib dipilih.',
            'class_id.exists' => 'Kelas tidak valid.',
            'nisn.max' => 'NISN maksimal 20 karakter.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'name.required' => 'Nama siswa wajib diisi.',
            'name.max' => 'Nama siswa maksimal 255 karakter.',
            'parent_name.required' => 'Nama orang tua wajib diisi.',
            'parent_name.max' => 'Nama orang tua maksimal 255 karakter.',
        ];
    }
}
