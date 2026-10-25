<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Phân quyền admin do TV7 xử lý bằng BookPolicy / middleware
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Form luôn gửi ít nhất một ô chọn tác giả / thể loại, bỏ các ô để trống "-- Chọn --"
        foreach (['author_ids', 'category_ids'] as $field) {
            if (is_array($this->input($field))) {
                $this->merge([$field => array_values(array_filter($this->input($field), 'filled'))]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            // Cột published_year kiểu YEAR của MySQL chỉ nhận 1901 - 2155
            'published_year' => ['nullable', 'integer', 'min:1901', 'max:'.now()->year],
            'page_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'dimensions' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'author_ids' => ['nullable', 'array'],
            'author_ids.*' => ['integer', 'distinct', 'exists:authors,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'isbn' => 'mã sách (ISBN)',
            'title' => 'tên sách',
            'price' => 'giá',
            'published_year' => 'năm xuất bản',
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
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'string' => ':Attribute phải là chuỗi ký tự.',
            'numeric' => ':Attribute phải là số.',
            'integer' => ':Attribute phải là số nguyên.',
            'array' => ':Attribute không hợp lệ.',
            'distinct' => ':Attribute bị chọn trùng.',
            'exists' => ':Attribute đã chọn không tồn tại.',
            'unique' => ':Attribute đã tồn tại.',
            'min.numeric' => ':Attribute không được nhỏ hơn :min.',
            'max.numeric' => ':Attribute không được lớn hơn :max.',
            'max.string' => ':Attribute không được vượt quá :max ký tự.',
            'image' => ':Attribute phải là tệp hình ảnh.',
            'mimes' => ':Attribute chỉ chấp nhận định dạng: :values.',
            'max.file' => ':Attribute không được vượt quá :max KB.',
            'cover_image.uploaded' => 'Tải ảnh bìa thất bại, có thể do tệp quá lớn.',
        ];
    }
}
