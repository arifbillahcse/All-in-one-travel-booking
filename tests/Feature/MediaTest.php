<?php

namespace Tests\Feature;

use App\Filament\Resources\DestinationResource\Pages\EditDestination;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Models\Destination;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
    }

    private function photo(string $name = 'photo.jpg', int $w = 2400, int $h = 1400): UploadedFile
    {
        return UploadedFile::fake()->image($name, $w, $h);
    }

    public function test_uploaded_photos_are_resized_to_webp_in_several_widths(): void
    {
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();
        $destination->addMedia($this->photo())->toMediaCollection('hero');

        $media = $destination->getFirstMedia('hero');
        foreach ([640, 1280, 1920] as $width) {
            $this->assertTrue($media->hasGeneratedConversion("hero-{$width}"), "hero-{$width}");
            $this->assertStringEndsWith('.webp', $media->getPath("hero-{$width}"));
            $this->assertSame($width, getimagesize($media->getPath("hero-{$width}"))[0]);
        }

        $this->assertSame(2400, $media->getCustomProperty('width'));
        $this->assertSame(1400, $media->getCustomProperty('height'));
    }

    public function test_pages_use_the_uploaded_photo_with_srcset_and_real_dimensions(): void
    {
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();
        $destination->addMedia($this->photo())->toMediaCollection('hero');
        $destination->addMedia($this->photo('c.jpg', 1600, 1200))->toMediaCollection('card');

        $html = $this->get('/destinations/sylhet')->getContent();
        $this->assertStringContainsString('hero-1920.webp', $html);
        $this->assertMatchesRegularExpression('/srcset="[^"]*hero-640\.webp 640w, [^"]*hero-1280\.webp 1280w, [^"]*hero-1920\.webp 1920w"/', $html);
        $this->assertStringContainsString('width="1920" height="1120"', $html);   // 2400x1400 scaled to 1920 keeps the ratio

        $this->get('/')->assertSee('card-800.webp', false);
        $this->get('/packages')->assertSee('card-800.webp', false);
    }

    public function test_without_uploads_the_placeholder_photos_are_used(): void
    {
        $this->get('/destinations/sylhet')->assertSee('picsum.photos/seed/sylhet-hero', false);
        $this->get('/blog')->assertSee('picsum.photos/seed/blog-', false);
    }

    public function test_gallery_photos_follow_the_caption_order_and_fill_gaps_with_placeholders(): void
    {
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();
        $destination->addMedia($this->photo('one.jpg'))->toMediaCollection('gallery');

        $html = $this->get('/destinations/sylhet')->getContent();

        $this->assertStringContainsString('gallery-1600.webp', $html);          // photo 1 uploaded
        $this->assertStringContainsString('picsum.photos/seed/sylhet-g2', $html); // photo 2 still a placeholder
    }

    public function test_blog_cover_is_served_from_an_upload(): void
    {
        Post::where('slug', 'cox-bazar-3-days')->firstOrFail()->addMedia($this->photo('cover.jpg', 1800, 1000))->toMediaCollection('cover');

        $this->get('/blog/cox-bazar-3-days')->assertSee('cover-1200.webp', false);
        $this->get('/blog')->assertSee('cover-1000.webp', false);   // featured card
    }

    public function test_admin_can_upload_a_photo_and_switching_language_keeps_it(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->create(['role' => 'editor']));
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();

        Livewire::test(EditDestination::class, ['record' => $destination->getKey()])
            ->fillForm(['hero' => $this->photo('first.jpg')])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(1, $destination->fresh()->getMedia('hero')->count());
        $uuid = $destination->fresh()->getFirstMedia('hero')->uuid;

        // edit in Bangla, switch back and save again: the photo must still be there exactly once
        Livewire::test(EditDestination::class, ['record' => $destination->getKey()])
            ->set('activeLocale', 'bn')
            ->fillForm(['tagline' => 'নতুন'])
            ->set('activeLocale', 'en')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(1, $destination->fresh()->getMedia('hero')->count());
        $this->assertSame($uuid, $destination->fresh()->getFirstMedia('hero')->uuid, 'the same file, not re-uploaded');
        $this->assertSame('নতুন', $destination->fresh()->getTranslation('tagline', 'bn'));
    }

    public function test_a_single_photo_collection_keeps_only_the_latest_upload(): void
    {
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();
        $destination->addMedia($this->photo('first.jpg'))->toMediaCollection('hero');
        $first = $destination->fresh()->getFirstMedia('hero')->uuid;

        $destination->addMedia($this->photo('second.jpg'))->toMediaCollection('hero');

        $this->assertCount(1, $destination->fresh()->getMedia('hero'));
        $this->assertNotSame($first, $destination->fresh()->getFirstMedia('hero')->uuid);
    }

    public function test_admin_rejects_files_that_are_not_images(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->create(['role' => 'editor']));
        $post = Post::first();

        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->fillForm(['cover' => UploadedFile::fake()->create('evil.php', 10, 'text/x-php')])
            ->call('save')
            ->assertHasFormErrors(['cover']);

        $this->assertCount(0, $post->fresh()->getMedia('cover'));
    }
}
