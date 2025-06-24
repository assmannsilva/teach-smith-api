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
            'email' => 'required|email',
            'registration_code' => 'required|max:255',
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'grade' => 'required|string',
            'section' => 'required|string',
            'admission_date' => 'required|date_format:Y-m-d',
        ];
    }
}
