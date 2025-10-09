<?php

namespace App\Http\Requests\EventCategory;

use Illuminate\Foundation\Http\FormRequest;

class EventCategoryStoreRequest extends FormRequest
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
            'lang_id' => 'required',
            'name' => 'required|max:255',
            'status' => 'required',
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
            'name.required' => 'The category name field is required',
            'status.required' => 'The status field is required',
            'lang_id.required' => 'The language field is required',
        ];
    }
}
