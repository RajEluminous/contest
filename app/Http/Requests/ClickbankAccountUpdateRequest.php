<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClickbankAccountUpdateRequest extends FormRequest
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
            'name' => 'required|min:3|unique:clickbank_accounts,name,' . $this->clickbank_acc->id,
        ];
    }
}
