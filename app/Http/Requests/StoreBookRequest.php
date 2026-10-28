<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Chuẩn hóa dữ liệu trước khi kiểm tra: bỏ dấu gạch / khoảng trắng trong ISBN
     * và bỏ các lựa chọn tác giả / thể loại để trống.
     */
    protected function prepareForValidation(): void
    {
        $normalized = [];

        if (is_string($this->input('isbn'))) {
            $isbn = preg_replace('/[\s-]+/', '', $this->input('isbn'));
            $normalized['isbn'] = $isbn === '' ? null : $isbn;
        }

        foreach (['author_ids', 'category_ids'] as $field) {
            if (is_array($this->input($field))) {
                $normalized[$field] = array_values(array_filter(
                    $this->input($field),
                    fn (mixed $id): bool => $id !== null && $id !== '',
                ));
            }
        }

        if (is_array($this->input('new_authors'))) {
            $normalized['new_authors'] = array_values(array_unique(array_filter(
                array_map(fn (mixed $name): string => is_string($name) ? trim($name) : '', $this->input('new_authors')),
                fn (string $name): bool => $name !== '',
            )));
        }

        $this->merge($normalized);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')],
            'author_ids' => ['required_without:new_authors', 'nullable', 'array'],
            'author_ids.*' => ['required', 'integer', 'distinct', Rule::exists('authors', 'id')],
            'new_authors' => ['nullable', 'array'],
            'new_authors.*' => ['required', 'string', 'max:255'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['required', 'integer', 'distinct', Rule::exists('categories', 'id')],
            'publisher_id' => ['required', 'integer', Rule::exists('publishers', 'id')],
            'published_year' => ['required', 'integer', 'min:1901', 'max:'.now()->year],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'page_count' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'dimensions' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:10000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'Tên sách',
            'isbn' => 'Mã sách / ISBN',
            'author_ids' => 'Tác giả',
            'author_ids.*' => 'Tác giả',
            'new_authors' => 'Tác giả mới',
            'new_authors.*' => 'Tên tác giả mới',
            'category_ids' => 'Thể loại',
            'category_ids.*' => 'Thể loại',
            'publisher_id' => 'Nhà xuất bản',
            'published_year' => 'Năm xuất bản',
            'price' => 'Giá bán',
            'stock_quantity' => 'Số lượng tồn kho',
            'page_count' => 'Số trang',
            'dimensions' => 'Kích thước',
            'description' => 'Mô tả sách',
            'cover_image' => 'Ảnh bìa',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'required_without' => ':attribute là bắt buộc.',
            'string' => ':attribute phải là văn bản.',
            'array' => ':attribute phải là danh sách lựa chọn.',
            'integer' => ':attribute phải là số nguyên.',
            'numeric' => ':attribute phải là một số hợp lệ.',
            'exists' => ':attribute đã chọn không tồn tại. Vui lòng chọn lại.',
            'distinct' => ':attribute không được chọn trùng.',
            'isbn.unique' => 'Mã sách / ISBN đã tồn tại.',
            'author_ids.min' => 'Vui lòng chọn ít nhất một tác giả.',
            'new_authors.*.max' => 'Tên tác giả mới không được vượt quá 255 ký tự.',
            'category_ids.min' => 'Vui lòng chọn ít nhất một thể loại.',
            'title.max' => 'Tên sách không được vượt quá 255 ký tự.',
            'isbn.max' => 'Mã sách / ISBN không được vượt quá 20 ký tự.',
            'published_year.min' => 'Năm xuất bản phải từ 1901 trở về sau.',
            'published_year.max' => 'Năm xuất bản không được lớn hơn năm hiện tại (:max).',
            'price.min' => 'Giá bán không được âm.',
            'price.max' => 'Giá bán không được vượt quá 9.999.999.999,99 VNĐ.',
            'price.decimal' => 'Giá bán chỉ được có tối đa hai chữ số thập phân.',
            'stock_quantity.min' => 'Số lượng tồn kho không được âm.',
            'stock_quantity.max' => 'Số lượng tồn kho không được vượt quá 4.294.967.295.',
            'page_count.min' => 'Số trang phải lớn hơn 0.',
            'page_count.max' => 'Số trang không được vượt quá 4.294.967.295.',
            'dimensions.max' => 'Kích thước không được vượt quá 50 ký tự.',
            'description.max' => 'Mô tả sách không được vượt quá 10.000 ký tự.',
            'cover_image.uploaded' => 'Tải ảnh bìa lên thất bại. Vui lòng chọn lại ảnh.',
            'cover_image.image' => 'Ảnh bìa phải là file hình ảnh.',
            'cover_image.mimes' => 'Ảnh bìa chỉ nhận định dạng JPG, JPEG, PNG hoặc WEBP.',
            'cover_image.max' => 'Ảnh bìa không được vượt quá 2MB.',
        ];
    }

    /**
     * Dữ liệu các cột của bảng books đã kiểm tra, không gồm bảng nối tác giả / thể loại và file ảnh.
     *
     * @return array<string, mixed>
     */
    public function bookData(): array
    {
        return Arr::except($this->validated(), ['author_ids', 'new_authors', 'category_ids', 'cover_image']);
    }

    /**
     * Lưu ảnh bìa vừa upload vào storage/app/public/covers với tên ngẫu nhiên.
     * Trả về đường dẫn để ghi vào cột cover_image_url, không chọn ảnh thì trả về null.
     */
    public function storeCover(): ?string
    {
        if (! $this->hasFile('cover_image')) {
            return null;
        }

        return $this->file('cover_image')->store(Book::COVER_DIR, 'public') ?: null;
    }
}
