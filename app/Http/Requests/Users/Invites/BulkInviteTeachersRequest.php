<?php

namespace App\Http\Requests\Users\Invites;

use Illuminate\Foundation\Http\FormRequest;

class BulkInviteTeachersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['import_file' => ['required', 'file', 'mimes:csv,txt','max:10240'] ];
    }
}
