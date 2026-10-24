<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ton_kho', function (Blueprint $table) {
            $table->foreignId('ma_sach')
                  ->constrained('sach', 'ma_sach')
                  ->cascadeOnDelete();
            $table->foreignId('ma_kho')
                  ->constrained('kho', 'ma_kho')
                  ->cascadeOnDelete();
            $table->unsignedInteger('so_luong_ton')->default(0);
            $table->primary(['ma_sach', 'ma_kho']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ton_kho');
    }
};
