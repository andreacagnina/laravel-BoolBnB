<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'property_id' => 'required|exists:properties,id',
        ];
    }
    public function messages()
    {
        return [
            'first_name.string' => 'The first name must be a valid string.',
            'first_name.max' => 'The first name may not be greater than 50 characters.',
            'last_name.string' => 'The last name must be a valid string.',
            'last_name.max' => 'The last name may not be greater than 50 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'message.required' => 'The message field is required.',
            'message.string' => 'The message must be a valid string.',
            'property_id.required' => 'The property ID is required.',
            'property_id.exists' => 'The selected property ID is invalid.',
        ];
    }
}
