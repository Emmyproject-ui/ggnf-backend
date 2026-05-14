<?php

namespace App\Http\Requests\Donation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth is handled by middleware
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:100|max:10000000',
            'payment_reference' => 'required|string|max:255|unique:donations,payment_reference',
            'cause' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|in:paystack,bank_transfer,cash',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Donation amount is required',
            'amount.min' => 'Minimum donation amount is ₦100',
            'amount.max' => 'Maximum donation amount is ₦10,000,000',
            'payment_reference.required' => 'Payment reference is required',
            'payment_reference.unique' => 'This payment reference has already been used',
            'payment_method.in' => 'Invalid payment method',
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
