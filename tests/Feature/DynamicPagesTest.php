<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Post;
use App\Models\Review;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_home_renders_database_content(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertSame(6, substr_count($html, 'class="card destination"'));
        $this->assertSame(3, substr_count($html, '<article class="package'));
        $this->assertSame(3, substr_count($html, '<article class="blog-card'));
        $this->assertStringContainsString('Rahim Uddin', $html);
        $this->assertStringNotContainsString('data.js', $html);
        $this->assertStringNotContainsString('blog-core.js', $html);
    }

    public function test_content_changes_in_the_database_show_on_the_site(): void
    {
        Package::where('slug', 'explorer')->first()->update(['price' => 13000]);
        Destination::where('slug', 'sylhet')->first()->update(['price_from' => 5900]);

        $this->get('/packages')->assertSee('13,000')->assertDontSee('12,500');
        $this->get('/destinations/sylhet')->assertSee('৳5,900', false);
        $this->get('/bn/packages')->assertSee('১৩,০০০');
    }

    public function test_destination_page_is_rendered_from_the_database_in_both_languages(): void
    {
        $this->get('/destinations/sundarbans')
            ->assertOk()
            ->assertSee('Board the launch and visit Karamjal')
            ->assertSee('Will I see a Royal Bengal tiger?')
            ->assertSee('Hiron Point (Nilkamal)')
            ->assertSee('<title>Sundarbans Tour Packages | TravelOrio</title>', false);

        $this->get('/bn/destinations/sundarbans')
            ->assertOk()
            ->assertSee('করমজল')
            ->assertSee('দিন ১')
            ->assertSee('সুন্দরবন ট্যুর প্যাকেজ');
    }

    public function test_unpublished_destination_is_hidden_everywhere(): void
    {
        Destination::where('slug', 'kuakata')->first()->update(['is_published' => false]);

        $this->get('/destinations/kuakata')->assertNotFound();
        $this->get('/')->assertDontSee('/destinations/kuakata', false);
        $this->get('/packages')->assertDontSee('/destinations/kuakata', false);
    }

    public function test_reviews_page_summary_filter_sort_and_show_more(): void
    {
        $this->get('/reviews')
            ->assertSee('Based on 12 traveler reviews')
            ->assertSee('4.9')
            ->assertSee('Showing 6 of 12 reviews')
            ->assertSee('Show more reviews');

        $this->get('/reviews?show=12')->assertSee('Showing 12 of 12 reviews')->assertDontSee('Show more reviews');
        $this->get('/reviews?destination=sylhet')->assertSee('Showing 2 of 2 reviews')->assertSee('Imran Hossain');
        $this->assertSame(2, substr_count($this->get('/reviews?destination=sylhet')->getContent(), 'review review--card'));
        $this->get('/bn/reviews?destination=sylhet')->assertSee('২টির মধ্যে ২টি রিভিউ দেখানো হচ্ছে');
    }

    public function test_review_sort_orders_by_rating_then_date(): void
    {
        $html = $this->get('/reviews?sort=highest&show=12')->getContent();
        $positions = [strpos($html, 'Rahim Uddin'), strpos($html, 'Farzana Akter')];

        $this->assertLessThan($positions[1], $positions[0], 'Five-star reviews come before four-star ones');
    }

    public function test_bad_review_parameters_fall_back_to_defaults(): void
    {
        $this->get('/reviews?destination=nope&sort=weird&show=-5')
            ->assertOk()
            ->assertSee('Showing 6 of 12 reviews');
    }

    public function test_unapproved_reviews_are_never_shown(): void
    {
        Review::query()->where('name->en', 'Rahim Uddin')->update(['is_approved' => false]);

        $this->get('/reviews?show=12')->assertDontSee('Rahim Uddin')->assertSee('Based on 11 traveler reviews');
        $this->get('/')->assertDontSee('Rahim Uddin');
    }

    public function test_blog_lists_filters_searches_and_paginates(): void
    {
        $html = $this->get('/blog')->assertOk()->assertSee('6 articles')->getContent();
        $this->assertSame(1, substr_count($html, 'blog-card--featured'));

        $this->get('/blog?category=guides')->assertSee('3 articles')->assertDontSee('blog-card--featured', false);
        $this->get('/blog?q=tea')->assertSee('Sylhet on a plate')->assertSee('1 article')->assertDontSee('first-timer');
        $this->get('/blog?q=zzzzz')->assertSee('No articles match your search.');
        $this->get('/bn/blog?q='.urlencode('চা'))->assertSee('থালায় সিলেট');
    }

    public function test_blog_search_treats_like_wildcards_literally(): void
    {
        $this->get('/blog?q=%25')->assertSee('No articles match your search.');
    }

    public function test_article_page_has_toc_navigation_related_and_trip_card(): void
    {
        $html = $this->get('/blog/cox-bazar-3-days')
            ->assertOk()
            ->assertSee('In this article')
            ->assertSee('Plan this trip')
            ->assertSee('Sundarbans: what to expect', false)   // older article = previous link
            ->getContent();

        $this->assertSame(3, substr_count($html, '<article class="blog-card'), 'three related articles');
        $this->assertStringContainsString('href="'.url('/packages').'?place=coxs-bazar"', $html);
        $this->assertStringContainsString('<title>Cox&#039;s Bazar in 3 days: a relaxed itinerary | TravelOrio</title>', $html);
    }

    public function test_draft_and_scheduled_articles_are_not_public(): void
    {
        Post::where('slug', 'cox-bazar-3-days')->update(['is_published' => false]);
        Post::where('slug', 'sylhet-tea-and-food')->update(['published_at' => now()->addDay()]);

        $this->get('/blog/cox-bazar-3-days')->assertNotFound();
        $this->get('/blog/sylhet-tea-and-food')->assertNotFound();
        $this->get('/blog')->assertSee('4 articles');
    }

    public function test_article_in_bangla_uses_bangla_content_and_digits(): void
    {
        $this->get('/bn/blog/cox-bazar-3-days')
            ->assertSee('তিন দিনে কক্সবাজার')
            ->assertSee('১২ মার্চ, ২০২৬')
            ->assertSee('৬ মিনিটে পড়া');
    }

    public function test_packages_page_builds_plans_comparison_addons_and_forms_from_the_database(): void
    {
        $html = $this->get('/packages')->assertOk()->getContent();

        $this->assertSame(3, substr_count($html, '<article class="package'));
        $this->assertSame(4, substr_count($html, 'class="addon"'));
        $this->assertSame(6, substr_count($html, 'class="card destination"'));
        $this->assertMatchesRegularExpression('/<option value="Explorer"[^>]*data-price="12500"/', $html);
        $this->assertStringContainsString('data-slug="sylhet"', $html);
        $this->assertStringContainsString('from ৳4,000 / day', $html);
    }
}
