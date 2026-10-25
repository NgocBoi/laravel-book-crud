<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class UpdateBookRequest extends StoreBookRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // ISBN là mã định danh, không cho sửa sau khi tạo (form cũng để readonly)
        return Arr::except(parent::rules(), 'isbn');
    }
}
