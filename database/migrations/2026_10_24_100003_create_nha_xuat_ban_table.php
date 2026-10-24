<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nha_xuat_ban', function (Blueprint $table) {
            $table->id('ma_nha_xuat_ban');
            $table->string('ten_nha_xuat_ban', 150)->unique();
            $table->string('dia_chi', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nha_xuat_ban');
    }
};
