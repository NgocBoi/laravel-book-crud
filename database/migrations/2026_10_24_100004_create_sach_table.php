<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sach', function (Blueprint $table) {
            $table->id('ma_sach');
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('ten_sach', 255);
            $table->text('mo_ta')->nullable();
            $table->unsignedSmallInteger('nam_xuat_ban')->nullable();
            $table->decimal('gia_ban', 12, 2)->default(0);
            $table->foreignId('ma_nha_xuat_ban')
                  ->constrained('nha_xuat_ban', 'ma_nha_xuat_ban')
                  ->restrictOnDelete();
            $table->foreignId('ma_the_loai')
                  ->constrained('the_loai', 'ma_the_loai')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sach');
    }
};