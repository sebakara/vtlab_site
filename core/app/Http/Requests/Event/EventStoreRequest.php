<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
            'date' => 'required',
            'time' => 'required',
            'cost' => 'required',
            'available_tickets' => 'required',
            'organizer' => 'required',
            'venue' => 'required',
            'image' => 'required',
            'lang_id' => 'required',
            'cat_id' => 'required',
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
            'date.required' => 'The date field is required',
            'time.required' => 'The time field is required',
            'cost.required' => 'The cost field is required',
            'available_tickets.required' => 'Number of tickets field is required',
            'organizer.required' => 'The organizer name field is required',
            'venue.required' => 'The venue field is required',
            'image.required' => 'The slider image field is required',
            'lang_id.required' => 'The language field is required',
            'cat_id.required' => 'The category field is required'
        ];
    }
}
