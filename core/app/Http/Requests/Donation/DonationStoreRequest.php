<?php

namespace App\Http\Requests\Donation;

use Illuminate\Foundation\Http\FormRequest;

class DonationStoreRequest extends FormRequest
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
            'title' => 'required|max:255',
            'goal_amount' => 'required',
            'min_amount' => 'required',
            'lang_id' => 'required',
            'image' => 'required',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'The title field is required',
            'goal_amount.required' => 'The goal amount field is required',
            'min_amount.required' => 'The minimum amount field is required',
            'lang_id.required' => 'The language field is required',
            'image.required' => 'The imageis field is required',
        ];
    }

}
