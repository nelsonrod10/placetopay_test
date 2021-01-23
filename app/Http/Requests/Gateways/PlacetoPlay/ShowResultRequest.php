<?php

namespace App\Http\Requests\Gateways\PlacetoPlay;

use Illuminate\Foundation\Http\FormRequest;

class ShowResultRequest extends FormRequest
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
            'reference' => 'required|string|exists:orders,number',
            'status'    => 'required|array'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'reference'  => __('translations.reference'),
            'status'     => __('translations.status'),
        ];
    }
}
