<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sach extends Model
{
    protected $table = 'sach';

    protected $primaryKey = 'ma_sach';

    public $timestamps = false;

    protected $fillable = [
        'isbn',
        'ten_sach',
        'mo_ta',
        'nam_xuat_ban',
        'gia_ban',
        'ma_nha_xuat_ban',
        'ma_the_loai',
    ];

    protected $casts = [
        'nam_xuat_ban' => 'integer',
        'gia_ban' => 'decimal:2',
    ];
}
