<?php

namespace App\Http\Requests\Users\Invites;

use Illuminate\Foundation\Http\FormRequest;

class InviteStudentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'students' => 'required|array|max:100',
            'students.*.name' => 'required|string|max:255',
            'students.*.email' => 'required|email',
            'students.*.registration_code' => 'required|string|cpf',
            'students.*.first_name' => 'required|string|max:255',
            'students.*.surname' => 'required|string|max:255',
            'students.*.grade_level' => 'required|string',
            'students.*.admission_date' => 'required|date_format:Y-m-d',
        ];
    }
}
