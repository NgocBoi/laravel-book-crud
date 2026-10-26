<?php

namespace App\Http\Requests;

use Illuminate\Support\Arr;

class UpdateBookRequest extends StoreBookRequest
{
    /**
     * Giống khi thêm mới (kế thừa StoreBookRequest), trừ mã sách (ISBN) chỉ đọc sau khi đã tạo.
     * Bỏ isbn khỏi rules nên dù client cố gửi lên cũng không lọt vào dữ liệu đã validate.
     */
    public function rules(): array
    {
        return Arr::except(parent::rules(), ['isbn']);
    }
}
