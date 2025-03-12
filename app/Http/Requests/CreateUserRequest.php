<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'confirm_password' => 'required|same:password',
            'phone_number' => 'nullable|regex:/^\+?[0-9]{7,15}$/',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            'date_of_birth' => 'nullable|date|before:today|after_or_equal:' . now()->subYears(100)->format('Y-m-d'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email address is already registered. Please use a different email address.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'The password must include a mix of uppercase, lowercase, numbers, and special characters.',
            'confirm_password.required' => 'The confirm password field is required.',
            'confirm_password.same' => 'The confirm password must match the password.',
            'phone_number.regex' => 'The phone number must be a valid format.',
            'address.max' => 'The address may not be greater than 255 characters.',
            'profile_picture.image' => 'The profile picture must be an image.',
            'profile_picture.max' => 'The profile picture may not be greater than 2MB.',
            'date_of_birth.date' => 'The date of birth must be a valid date.',
            'date_of_birth.before' => 'The date of birth must be a date before today.',
            'date_of_birth.after_or_equal' => 'The date of birth must be a date after or equal to 100 years ago.',
        ];
    }
}
