<?php

namespace App\Http\Requests\Volunteer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreVolunteerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth is handled by middleware
    }

    public function rules(): array
    {
        return [
            'skills' => 'required|string|max:1000',
            'availability' => 'required|string|in:weekdays,weekends,flexible,full-time,part-time',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'message' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'skills.required' => 'Please describe your skills',
            'skills.max' => 'Skills description is too long',
            'availability.required' => 'Please specify your availability',
            'availability.in' => 'Invalid availability option',
            'phone.regex' => 'Please provide a valid phone number',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
