<?php

namespace App\Http\Requests;

use App\Models\Book;
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
        $rules['isbn'] = $this->isbnRules();

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...parent::messages(),
            'isbn.in' => 'Mã sách / ISBN không được thay đổi khi sửa sách.',
        ];
    }

    /**
     * Mã sách chỉ đọc khi sửa: sách đã có ISBN thì không được đổi hay xóa (không gửi lên thì giữ nguyên);
     * sách chưa có ISBN thì được nhập lần đầu, không trùng sách khác.
     *
     * @return array<int, mixed>
     */
    private function isbnRules(): array
    {
        $book = $this->route('book');
        $currentIsbn = $book instanceof Book ? $book->isbn : null;

        if (filled($currentIsbn)) {
            return ['sometimes', Rule::in([$currentIsbn])];
        }

        return ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book)];
    }
}
