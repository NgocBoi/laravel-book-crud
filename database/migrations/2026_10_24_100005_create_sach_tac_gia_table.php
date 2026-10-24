<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sach_tac_gia', function (Blueprint $table) {
            $table->foreignId('ma_sach')
                  ->constrained('sach', 'ma_sach')
                  ->cascadeOnDelete();
            $table->foreignId('ma_tac_gia')
                  ->constrained('tac_gia', 'ma_tac_gia')
                  ->restrictOnDelete();
            $table->primary(['ma_sach', 'ma_tac_gia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sach_tac_gia');
    }
};
