<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Review;
use App\Models\Setting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_seeders_import_all_legacy_content(): void
    {
        $this->assertSame(6, Destination::count());
        $this->assertSame(3, Package::count());
        $this->assertSame(4, Addon::count());
        $this->assertSame(12, Review::count());
        $this->assertSame(6, Post::count());
        $this->assertSame(4, PostCategory::count());
    }

    public function test_seeding_twice_creates_no_duplicates(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(6, Destination::count());
        $this->assertSame(12, Review::count());
        $this->assertSame(6, Post::count());
        $this->assertSame(4, PostCategory::count());
    }

    public function test_destination_is_translated_by_locale(): void
    {
        $destination = Destination::where('slug', 'coxs-bazar')->first();

        app()->setLocale('en');
        $this->assertSame("Cox's Bazar", $destination->name);
        $this->assertSame('Laboni Beach', $destination->attractions[0]['name']);

        app()->setLocale('bn');
        $this->assertSame('কক্সবাজার', $destination->name);
        $this->assertSame('লাবণী সৈকত', $destination->attractions[0]['name']);
    }

    public function test_bangla_nested_content_keeps_non_translated_fields(): void
    {
        app()->setLocale('bn');
        $destination = Destination::where('slug', 'sundarbans')->first();

        $this->assertNotEmpty($destination->attractions[0]['img']);
        $this->assertContains($destination->seasons[0]['tone'], ['best', 'good', 'wet']);
    }

    public function test_every_destination_has_matching_english_and_bangla_structure(): void
    {
        foreach (Destination::all() as $destination) {
            foreach ($destination->translatable as $field) {
                $en = $destination->getTranslation($field, 'en');
                $bn = $destination->getTranslation($field, 'bn', false);

                $this->assertNotEmpty($bn, "{$destination->slug}.{$field} has no Bangla");
                $this->assertSame(
                    is_array($en) ? count($en) : 1,
                    is_array($bn) ? count($bn) : 1,
                    "{$destination->slug}.{$field} differs between English and Bangla"
                );
            }
        }
    }

    public function test_every_post_has_matching_block_structure_in_both_languages(): void
    {
        foreach (Post::all() as $post) {
            $en = $post->getTranslation('body', 'en');
            $bn = $post->getTranslation('body', 'bn');

            $this->assertSame(array_column($en, 'type'), array_column($bn, 'type'), $post->slug);
        }
    }

    public function test_missing_translation_falls_back_to_english(): void
    {
        $package = Package::where('slug', 'explorer')->first();
        $package->forgetAllTranslations('bn')->save();

        app()->setLocale('bn');
        $this->assertSame('Explorer', $package->fresh()->name);
    }

    public function test_relations(): void
    {
        $sylhet = Destination::where('slug', 'sylhet')->first();

        $this->assertSame(2, $sylhet->reviews()->count());
        $this->assertSame(['coxs-bazar', 'saint-martin', 'sundarbans'], Destination::where('slug', 'kuakata')->first()->related);
        $this->assertCount(3, Destination::where('slug', 'kuakata')->first()->relatedDestinations());

        $post = Post::where('slug', 'cox-bazar-3-days')->first();
        $this->assertSame('coxs-bazar', $post->destination->slug);
        $this->assertSame('guides', $post->category->slug);
    }

    public function test_published_scopes_hide_drafts_and_scheduled_posts(): void
    {
        Post::where('slug', 'cox-bazar-3-days')->update(['is_published' => false]);
        Post::where('slug', 'sylhet-tea-and-food')->update(['published_at' => now()->addWeek()]);
        Destination::where('slug', 'kuakata')->update(['is_published' => false]);
        Review::query()->first()->update(['is_approved' => false]);

        $this->assertSame(4, Post::published()->count());
        $this->assertSame(5, Destination::published()->count());
        $this->assertSame(11, Review::approved()->count());
    }

    public function test_packages_match_the_static_site(): void
    {
        $explorer = Package::where('slug', 'explorer')->first();

        $this->assertSame(12500, $explorer->price);
        $this->assertTrue($explorer->is_featured && $explorer->has_guide && ! $explorer->has_airport_transfer);
        $this->assertSame('Most Popular', $explorer->badge);
        $this->assertCount(5, $explorer->features);
        $this->assertSame(['weekend-escape', 'explorer', 'grand-bangladesh'], Package::published()->pluck('slug')->all());
    }

    public function test_inquiry_defaults_and_scopes(): void
    {
        Inquiry::factory()->count(2)->create();
        Inquiry::factory()->contact()->create(['status' => 'contacted']);

        $this->assertSame(2, Inquiry::new()->count());
        $this->assertSame(1, Inquiry::ofType(Inquiry::TYPE_CONTACT)->count());
    }

    public function test_settings_override_config_defaults(): void
    {
        $this->assertSame('hello@travelorio.com', site('email'));

        Setting::put('email', 'bookings@travelorio.com');

        $this->assertSame('bookings@travelorio.com', site('email'));
        $this->assertSame('8801779440297', site('whatsapp'));
    }

    public function test_deleting_a_destination_keeps_its_reviews_and_posts(): void
    {
        Destination::where('slug', 'sylhet')->first()->delete();

        $this->assertSame(12, Review::count());
        $this->assertNull(Review::whereNull('destination_id')->first()?->destination);
        $this->assertSame(6, Post::count());
    }
}
