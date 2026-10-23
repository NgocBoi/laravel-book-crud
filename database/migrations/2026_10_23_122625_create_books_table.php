<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('book_code', 50)->unique();
            $table->string('isbn', 20)->nullable()->index();
            $table->string('title');
            $table->string('author');
            $table->string('translator')->nullable();
            $table->string('publisher')->nullable();
            $table->string('supplier')->nullable();
            $table->integer('publish_year')->nullable();
            $table->string('language', 50)->default('Tiếng Việt');
            $table->integer('weight')->nullable();
            $table->string('dimensions', 100)->nullable();
            $table->integer('pages')->nullable();
            $table->decimal('original_price', 12, 2)->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('sold_quantity')->default(0);
            $table->string('cover_image')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_trending')->default(false);
            $table->string('badge_type', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
