<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['sometimes', 'required', 'string'],
            'phone' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255'],
            'logo' => ['sometimes', 'image', 'mimes:jpeg,png', 'max:2048'],
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
            'name.required' => 'Nama sekolah wajib diisi.',
            'address.required' => 'Alamat sekolah wajib diisi.',
            'phone.required' => 'Telepon sekolah wajib diisi.',
            'email.required' => 'Email sekolah wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'logo.image' => 'File harus berupa gambar.',
            'logo.mimes' => 'Format gambar hanya jpeg atau png.',
            'logo.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}