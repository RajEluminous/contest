<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrizeUpdateRequest extends FormRequest
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
            'name' => 'required|min:3|unique:prize,name,' . $this->id,
            'contest_id' => 'required|exists:App\Models\Prize,contest_id',
            'contest_type' => 'required',
            'column' => 'required',
            'column_label_1' => 'required',
        ];
    }
}
