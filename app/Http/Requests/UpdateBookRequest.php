<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateBookRequest extends StoreBookRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['isbn'] = ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($this->route('book'))];

        return $rules;
    }
}
