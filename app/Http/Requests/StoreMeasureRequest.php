<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMeasureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'systolic'  => ['nullable', 'integer', 'min:40', 'max:250'],
            'diastolic' => ['nullable', 'integer', 'min:40', 'max:250'],
            'pulse'     => ['nullable', 'integer', 'min:30', 'max:250'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->only(['systolic', 'diastolic', 'pulse']);
            $filled = array_filter($data, fn($v) => !is_null($v));

            if (empty($filled)) {
                $validator->errors()->add(
                    'general',
                    'At least one measurement (systolic, diastolic or pulse) is required.'
                );
            }
        });
    }
}
