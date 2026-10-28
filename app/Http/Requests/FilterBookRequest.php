<?php

namespace App\Http\Requests;

use App\Services\BookFilterService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * TV2 - Kiểm tra tham số lọc/sắp xếp trên query string của trang danh sách sách.
 */
class FilterBookRequest extends FormRequest
{
    /** Tham số sai: quay về danh sách gốc (không lặp vòng) kèm thông báo lỗi. */
    protected $redirectRoute = 'books.index';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'author_id' => ['nullable', 'integer', 'min:1'],
            'publisher_id' => ['nullable', 'integer', 'min:1'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', Rule::in(['in', 'low', 'out'])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'price_asc', 'price_desc'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keyword.string' => 'Từ khóa không hợp lệ.',
            'keyword.max' => 'Từ khóa tối đa 255 ký tự.',
            'category_id.integer' => 'Thể loại không hợp lệ.',
            'author_id.integer' => 'Tác giả không hợp lệ.',
            'publisher_id.integer' => 'Nhà xuất bản không hợp lệ.',
            'price_min.numeric' => 'Giá "từ" phải là số.',
            'price_min.min' => 'Giá "từ" không được âm.',
            'price_max.numeric' => 'Giá "đến" phải là số.',
            'price_max.min' => 'Giá "đến" không được âm.',
            'stock.in' => 'Tình trạng kho không hợp lệ.',
            'sort.in' => 'Kiểu sắp xếp không hợp lệ.',
        ];
    }
}