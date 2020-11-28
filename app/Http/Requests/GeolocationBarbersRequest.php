<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeolocationBarbersRequest extends FormRequest
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
            "city" => "nullable|string",
            "latitude" => "nullable|regex:/^(\-?\d+(\.\d+)?)$/",
            "longitude" => "nullable|regex:/^(\-?\d+(\.\d+)?)$/"
        ];
    }
}
