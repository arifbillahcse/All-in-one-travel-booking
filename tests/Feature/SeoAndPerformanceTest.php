<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Post;
use App\Models\Review;
use App\Models\Setting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SeoAndPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** @return list<array<string, mixed>> every JSON-LD graph item on a page */
    private function jsonLd(string $url): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $this->get($url)->getContent(), $matches);

        return collect($matches[1])->flatMap(function (string $json) {
            $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

            return $data['@graph'] ?? [$data];
        })->all();
    }

    private function types(array $graph): array
    {
        return array_column($graph, '@type');
    }

    // ---- robots and sitemap -----------------------------------------------------------------

    public function test_robots_txt_blocks_admin_and_points_to_the_sitemap(): void
    {
        $response = $this->get('/robots.txt')->assertOk();

        $this->assertStringStartsWith('text/plain', $response->headers->get('Content-Type'));
        $response->assertSee('Disallow: /admin')->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_sitemap_lists_every_public_page_in_both_languages_with_alternates(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $locs = array_map('strval', $xml->xpath('//s:loc'));

        $this->assertCount(36, $locs);   // (6 pages + 6 destinations + 6 articles) x 2 languages
        $this->assertContains(url('/destinations/sylhet'), $locs);
        $this->assertContains(url('/bn/destinations/sylhet'), $locs);
        $this->assertContains(url('/bn/blog/cox-bazar-3-days'), $locs);
        $this->assertStringContainsString('hreflang="bn" href="'.url('/bn/packages').'"', $response->getContent());
        $this->assertStringContainsString('hreflang="x-default" href="'.url('/packages').'"', $response->getContent());
        $this->assertStringNotContainsString('/admin', $response->getContent());
    }

    public function test_sitemap_follows_content_changes(): void
    {
        $this->get('/sitemap.xml')->assertSee('/destinations/kuakata', false);

        Destination::where('slug', 'kuakata')->firstOrFail()->update(['is_published' => false]);
        Post::where('slug', 'cox-bazar-3-days')->firstOrFail()->update(['is_published' => false]);

        $this->get('/sitemap.xml')->assertDontSee('/destinations/kuakata', false)->assertDontSee('cox-bazar-3-days', false);
    }

    // ---- head tags -----------------------------------------------------------------------------

    public function test_share_tags_and_default_image(): void
    {
        $this->get('/')
            ->assertSee('<meta property="og:image" content="'.url('images/og-default.png').'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertDontSee('name="robots"', false);

        $this->assertFileExists(public_path('images/og-default.png'));
    }

    public function test_article_pages_are_marked_as_articles(): void
    {
        $this->get('/blog/cox-bazar-3-days')
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('property="article:published_time" content="2026-03-12', false);
    }

    public function test_search_results_and_filtered_lists_are_not_indexed_but_clean_pages_are(): void
    {
        $this->get('/blog?q=tea')->assertSee('<meta name="robots" content="noindex,follow">', false);
        $this->get('/reviews?sort=highest')->assertSee('noindex,follow', false);
        $this->get('/reviews?destination=sylhet')->assertSee('noindex,follow', false);

        $this->get('/blog')->assertDontSee('noindex', false);
        $this->get('/reviews')->assertDontSee('noindex', false);
        // the canonical address never carries the filters
        $this->get('/blog?q=tea')->assertSee('<link rel="canonical" href="'.url('/blog').'">', false);
    }

    // ---- structured data ----------------------------------------------------------------------------

    public function test_every_page_describes_the_organisation_and_website(): void
    {
        foreach (['/', '/packages', '/bn/contact'] as $url) {
            $graph = $this->jsonLd($url);
            $this->assertContains('TravelAgency', $this->types($graph), $url);
            $this->assertContains('WebSite', $this->types($graph), $url);
        }
    }

    public function test_placeholder_social_links_are_not_published_as_profiles(): void
    {
        $org = collect($this->jsonLd('/'))->firstWhere('@type', 'TravelAgency');
        $this->assertArrayNotHasKey('sameAs', $org);

        Setting::put('social.facebook', 'https://facebook.com/travelorio');
        $org = collect($this->jsonLd('/'))->firstWhere('@type', 'TravelAgency');
        $this->assertSame(['https://facebook.com/travelorio'], $org['sameAs']);
    }

    public function test_destination_page_has_trip_faq_and_breadcrumb_data(): void
    {
        $graph = $this->jsonLd('/destinations/sylhet');
        $this->assertEqualsCanonicalizing(['TravelAgency', 'WebSite', 'TouristTrip', 'FAQPage', 'BreadcrumbList'], $this->types($graph));

        $trip = collect($graph)->firstWhere('@type', 'TouristTrip');
        $this->assertSame('Sylhet', $trip['name']);
        $this->assertSame('BDT', $trip['offers']['priceCurrency']);
        $this->assertSame((string) Destination::where('slug', 'sylhet')->value('price_from'), $trip['offers']['price']);

        $faq = collect($graph)->firstWhere('@type', 'FAQPage');
        $this->assertCount(5, $faq['mainEntity']);

        $crumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList')['itemListElement'];
        $this->assertSame(['Home', 'Sylhet'], array_column($crumbs, 'name'));
    }

    public function test_bangla_pages_describe_themselves_in_bangla(): void
    {
        $trip = collect($this->jsonLd('/bn/destinations/sylhet'))->firstWhere('@type', 'TouristTrip');

        $this->assertSame('সিলেট', $trip['name']);
        $this->assertSame('bn', $trip['inLanguage']);
        $this->assertSame(url('/bn/destinations/sylhet'), $trip['url']);
    }

    public function test_article_page_has_blogposting_data(): void
    {
        $post = collect($this->jsonLd('/blog/cox-bazar-3-days'))->firstWhere('@type', 'BlogPosting');

        $this->assertSame("Cox's Bazar in 3 days: a relaxed itinerary", $post['headline']);
        $this->assertStringStartsWith('2026-03-12', $post['datePublished']);
        $this->assertSame(url('/blog/cox-bazar-3-days'), $post['mainEntityOfPage']);
    }

    public function test_admin_text_can_never_break_out_of_the_json_ld_script_tag(): void
    {
        Destination::where('slug', 'sylhet')->firstOrFail()->setTranslation('tagline', 'en', 'Nice</script><script>alert(1)</script>')->save();

        $html = $this->get('/destinations/sylhet')->getContent();

        $this->assertStringNotContainsString('</script><script>alert(1)', $html);
        $this->assertStringContainsString('\u003C/script\u003E\u003Cscript\u003Ealert(1)', $html, 'the tags are escaped inside the JSON');
        $this->jsonLd('/destinations/sylhet');   // still valid JSON
    }

    // ---- speed ------------------------------------------------------------------------------------------

    public function test_scripts_are_versioned_so_browsers_refetch_after_a_change(): void
    {
        $this->get('/')->assertSee('assets/js/main.js?v='.filemtime(public_path('assets/js/main.js')), false);
    }

    public function test_menu_changes_show_immediately_despite_caching(): void
    {
        $this->get('/')->assertSee('Kuakata');

        Destination::where('slug', 'kuakata')->firstOrFail()->setTranslation('name', 'en', 'Kuakata Beach')->save();

        $this->get('/')->assertSee('Kuakata Beach');
    }

    public function test_page_queries_stay_flat_as_content_grows(): void
    {
        $urls = ['/', '/packages', '/reviews?show=60', '/blog', '/blog/cox-bazar-3-days', '/destinations/sylhet'];
        array_map(fn ($url) => $this->queries($url), $urls);   // warm the caches
        $before = array_map(fn ($url) => $this->queries($url), $urls);

        foreach (Destination::all() as $i => $destination) {
            Destination::create(array_merge($destination->getAttributes(), ['id' => null, 'slug' => "{$destination->slug}-copy"]));
        }
        foreach (Post::all() as $post) {
            Post::create(array_merge($post->getAttributes(), ['id' => null, 'slug' => "{$post->slug}-copy"]));
        }
        Review::factory()->count(15)->create(['destination_id' => Destination::first()->id]);

        array_map(fn ($url) => $this->queries($url), $urls);   // warm again after the content change
        $after = array_map(fn ($url) => $this->queries($url), $urls);

        $this->assertSame($before, $after, 'an N+1 query appeared: '.json_encode(['urls' => $urls, 'before' => $before, 'after' => $after]));
        $this->assertLessThanOrEqual(12, max($after));
    }

    private function queries(string $url): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get($url)->assertOk();

        return count(DB::getQueryLog());
    }
}
