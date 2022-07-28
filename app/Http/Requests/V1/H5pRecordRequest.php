<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class H5pRecordRequest extends FormRequest
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
            'playlist_id' => 'required',
            'activity_id' => 'required',
            'statement' => 'required',
            'activity_name' => 'required',
        ];
    }
}