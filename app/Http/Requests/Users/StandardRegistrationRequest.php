<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StandardRegistrationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:8|max:255|confirmed",
            "first_name" => "required|string|max:255",
            "surname" => "required|string|max:255",
            "organization_id" => "required|exists:organizations,id",
        ];
    }
}
