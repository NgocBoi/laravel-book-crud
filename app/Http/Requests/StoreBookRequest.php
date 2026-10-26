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

            // Khóa ngoại: chỉ nhận id đang tồn tại
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'author_ids' => ['required', 'array', 'min:1'],
            'author_ids.*' => ['integer', 'distinct', 'exists:authors,id'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],

            // Ảnh bìa: chỉ nhận file ảnh, tối đa 2MB
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Tên tiếng Việt của các trường, dùng trong thông báo lỗi.
     */
    public function attributes(): array
    {
        return [
            'isbn' => 'mã sách (ISBN)',
            'title' => 'tên sách',
            'published_year' => 'năm xuất bản',
            'price' => 'giá bán',
            'page_count' => 'số trang',
            'dimensions' => 'kích thước',
            'description' => 'mô tả',
            'publisher_id' => 'nhà xuất bản',
            'author_ids' => 'tác giả',
            'author_ids.*' => 'tác giả',
            'category_ids' => 'thể loại',
            'category_ids.*' => 'thể loại',
            'cover_image' => 'ảnh bìa',
        ];
    }

    /**
     * Thông báo lỗi tiếng Việt cho các rule đang dùng
     * (đặt ngay trong Request nên không phụ thuộc cấu hình ngôn ngữ của cả hệ thống).
     */
    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'author_ids.required' => 'Vui lòng chọn ít nhất một tác giả.',
            'category_ids.required' => 'Vui lòng chọn ít nhất một thể loại.',
            'string' => ':Attribute phải là chuỗi ký tự.',
            'integer' => ':Attribute phải là số nguyên.',
            'numeric' => ':Attribute phải là số.',
            'array' => ':Attribute không hợp lệ.',
            'distinct' => ':Attribute bị chọn trùng.',
            'exists' => ':Attribute đã chọn không tồn tại.',
            'unique' => ':Attribute này đã tồn tại.',
            'image' => ':Attribute phải là file ảnh.',
            'mimes' => ':Attribute phải có định dạng: :values.',
            'uploaded' => 'Tải lên :attribute thất bại (file có thể quá lớn).',
            'max' => [
                'string' => ':Attribute không được dài quá :max ký tự.',
                'numeric' => ':Attribute không được lớn hơn :max.',
                'file' => ':Attribute không được lớn hơn :max KB.',
            ],
            'min' => [
                'numeric' => ':Attribute phải lớn hơn hoặc bằng :min.',
                'array' => 'Vui lòng chọn ít nhất :min :attribute.',
            ],
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
