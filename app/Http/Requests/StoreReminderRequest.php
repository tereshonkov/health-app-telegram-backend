<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
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
            'name'    => ['required', 'string', 'max:255'],
            'dose'    => ['required', 'string', 'max:100'],
            'times'   => ['required', 'array', 'min:1', 'max:6'],
            'times.*' => ['required', 'string', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'times.*.regex' => 'Each time must be in HH:MM format (e.g. 08:00).',
            'times.min'     => 'At least one reminder time is required.',
            'times.max'     => 'Maximum 6 reminder times allowed.',
        ];
    }
}
