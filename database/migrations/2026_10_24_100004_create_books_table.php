<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('title', 255);
            $table->year('published_year')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('page_count')->nullable();
            $table->string('dimensions', 50)->nullable();
            $table->string('cover_image_url', 255)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('publisher_id')->nullable()
                  ->constrained('publishers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
