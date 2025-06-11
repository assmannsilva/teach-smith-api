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
            'email' => 'required|email',
            'cpf' => 'required|string|cpf',
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'degree' => 'required|string',
            'hire_date' => 'required|date_format:Y-m-d',
        ];
    }
}
