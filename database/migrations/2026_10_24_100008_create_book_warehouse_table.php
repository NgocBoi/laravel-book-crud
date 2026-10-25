<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_warehouse', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->primary(['book_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_warehouse');
    }
};
