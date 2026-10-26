<?php

namespace Tests\Feature;

use App\Services\BookCoverService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookCoverServiceTest extends TestCase
{
    private BookCoverService $covers;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->covers = new BookCoverService;
    }

    public function test_store_saves_file_in_covers_folder_with_random_name(): void
    {
        $path = $this->covers->store(UploadedFile::fake()->image('bia goc.jpg'));

        $this->assertStringStartsWith('covers/', $path);
        $this->assertStringNotContainsString('bia goc', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_store_returns_null_without_file(): void
    {
        $this->assertNull($this->covers->store(null));
    }

    public function test_delete_removes_stored_file_only(): void
    {
        $path = $this->covers->store(UploadedFile::fake()->image('bia.png'));

        $this->covers->delete($path);
        Storage::disk('public')->assertMissing($path);

        // Rỗng hoặc link ngoài: không làm gì, không lỗi
        $this->covers->delete(null);
        $this->covers->delete('https://cdn.example.com/bia.jpg');
        $this->assertTrue(true);
    }

    public function test_url_for_stored_file_and_external_link(): void
    {
        $this->assertSame('/storage/covers/abc.jpg', $this->covers->url('covers/abc.jpg'));
        $this->assertSame('https://cdn.example.com/bia.jpg', $this->covers->url('https://cdn.example.com/bia.jpg'));
        $this->assertNull($this->covers->url(null));
    }
}
