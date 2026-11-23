<?php

namespace App\Http\Requests;

use App\Models\GradeConversion;
use Illuminate\Foundation\Http\FormRequest;

class StoreGradeConversionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'min_score' => ['required', 'integer', 'min:0', 'max:100'],
            'max_score' => ['required', 'integer', 'min:0', 'max:100', 'gte:min_score'],
            'text_label' => ['required', 'string', 'max:50'],
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
            'min_score.required' => 'Nilai minimum wajib diisi.',
            'min_score.integer' => 'Nilai minimum harus berupa angka.',
            'min_score.min' => 'Nilai minimum minimal 0.',
            'min_score.max' => 'Nilai minimum maksimal 100.',
            'max_score.required' => 'Nilai maksimum wajib diisi.',
            'max_score.integer' => 'Nilai maksimum harus berupa angka.',
            'max_score.min' => 'Nilai maksimum minimal 0.',
            'max_score.max' => 'Nilai maksimum maksimal 100.',
            'max_score.gte' => 'Nilai maksimum harus lebih besar atau sama dengan nilai minimum.',
            'text_label.required' => 'Label teks wajib diisi.',
            'text_label.max' => 'Label teks maksimal 50 karakter.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $min = $this->input('min_score');
            $max = $this->input('max_score');

            if (is_numeric($min) && is_numeric($max)) {
                $overlap = GradeConversion::where(function ($query) use ($min, $max) {
                    $query->where('min_score', '<=', $max)
                        ->where('max_score', '>=', $min);
                })->exists();

                if ($overlap) {
                    $validator->errors()->add('max_score', 'Rentang nilai ini tumpang tindih dengan data yang sudah ada.');
                }
            }
        });
    }
}
