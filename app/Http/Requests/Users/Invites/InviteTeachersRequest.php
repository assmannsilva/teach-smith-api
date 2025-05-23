<?php

namespace App\Http\Requests\Users\Invites;

use Illuminate\Foundation\Http\FormRequest;

class InviteTeachersRequest extends FormRequest
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
            'teachers' => 'required|array|max:100',
            'teachers.*.name' => 'required|string|max:255',
            'teachers.*.email' => 'required|email',
            'teachers.*.cpf' => 'required|string|cpf',
            'teachers.*.first_name' => 'required|string|max:255',
            'teachers.*.surname' => 'required|string|max:255',
            'teachers.*.degree' => 'required|string',
            'teachers.*.hire_date' => 'required|date_format:Y-m-d',
        ];
    }
}
