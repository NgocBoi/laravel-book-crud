<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kho', function (Blueprint $table) {
            $table->id('ma_kho');
            $table->string('ten_kho', 150);
            $table->string('dia_chi', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kho');
    }
};
