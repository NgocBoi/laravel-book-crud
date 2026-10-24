<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('the_loai', function (Blueprint $table) {
            $table->id('ma_the_loai');
            $table->string('ten_the_loai', 150)->unique();
            $table->text('mo_ta')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('the_loai');
    }
};
