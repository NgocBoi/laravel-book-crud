<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Phân quyền admin/user sẽ gắn vào đây khi có chức năng đăng nhập.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isbn' => ['nullable', 'string', 'max:20', $this->uniqueIsbn()],
            'title' => ['required', 'string', 'max:255'],
            // Cột YEAR của MySQL chỉ nhận 1901 - 2155
            'published_year' => ['nullable', 'integer', 'min:1901', 'max:'.(now()->year + 1)],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'page_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'dimensions' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * ISBN không được trùng với sách đã có.
     */
    protected function uniqueIsbn(): Unique
    {
        return Rule::unique('books', 'isbn');
    }
}
