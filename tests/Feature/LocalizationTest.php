<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private const PAGES = ['', '/packages', '/why-us', '/reviews', '/blog', '/blog/cox-bazar-3-days', '/contact', '/destinations/sylhet'];

    public function test_every_page_exists_in_bangla_under_the_bn_prefix(): void
    {
        foreach (self::PAGES as $path) {
            $this->get('/bn'.$path)
                ->assertOk()
                ->assertSee('<html lang="bn">', false);

            $this->get($path === '' ? '/' : $path)
                ->assertOk()
                ->assertSee('<html lang="en">', false);
        }
    }

    public function test_bangla_pages_use_bangla_chrome_and_bangla_links(): void
    {
        $this->get('/bn')
            ->assertSee('প্যাকেজ')                       // navbar
            ->assertSee('কক্সবাজার')                       // destination name in the menu
            ->assertSee(url('/bn/packages'), false)       // links stay in Bangla
            ->assertSee(url('/bn/destinations/sylhet'), false)
            ->assertDontSee('>Packages<', false);
    }

    public function test_titles_and_descriptions_are_translated(): void
    {
        $this->get('/bn/why-us')->assertSee('<title>কেন আমাদের বেছে নেবেন | TravelOrio</title>', false);
        $this->get('/why-us')->assertSee('<title>Why Choose Us | TravelOrio</title>', false);
        $this->get('/bn/destinations/sylhet')->assertSee('<title>সিলেট ট্যুর প্যাকেজ | TravelOrio</title>', false);
    }

    public function test_pages_declare_hreflang_alternates_and_a_switcher(): void
    {
        $this->get('/bn/packages?plan=Explorer')
            ->assertSee('<link rel="alternate" hreflang="en" href="'.url('/packages').'?plan=Explorer">', false)
            ->assertSee('<link rel="alternate" hreflang="bn" href="'.url('/bn/packages').'?plan=Explorer">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/packages').'?plan=Explorer">', false)
            ->assertSee('<meta property="og:locale" content="bn_BD">', false)
            ->assertSee('hreflang="en" lang="en"', false);
    }

    public function test_switcher_keeps_the_same_page_and_marks_the_current_language(): void
    {
        $en = $this->get('/destinations/kuakata')->getContent();
        $this->assertStringContainsString('href="'.url('/bn/destinations/kuakata').'" hreflang="bn"', $en);
        $this->assertMatchesRegularExpression('/hreflang="en" lang="en"\s+aria-current="true"/', $en);

        $bn = $this->get('/bn/destinations/kuakata')->getContent();
        $this->assertStringContainsString('href="'.url('/destinations/kuakata').'" hreflang="en"', $bn);
        $this->assertMatchesRegularExpression('/hreflang="bn" lang="bn"\s+aria-current="true"/', $bn);
    }

    public function test_error_pages_follow_the_url_language(): void
    {
        $this->get('/bn/nowhere')->assertNotFound()->assertSee('হোমে ফিরে যান');
        $this->get('/nowhere')->assertNotFound()->assertSee('Back to home');
        $this->get('/bn/destinations/atlantis')->assertNotFound();
    }

    public function test_javascript_gets_language_aware_routes(): void
    {
        $this->get('/bn/blog')->assertSee('blog: "'.str_replace('/', '\/', url('/bn/blog')).'"', false);
        $this->get('/blog')->assertSee('blog: "'.str_replace('/', '\/', url('/blog')).'"', false);
    }

    public function test_number_money_and_date_helpers(): void
    {
        app()->setLocale('en');
        $this->assertSame('৳12,500', format_money(12500));
        $this->assertSame('March 12, 2026', format_date('2026-03-12'));
        $this->assertSame('3', to_locale_digits('3'));

        app()->setLocale('bn');
        Carbon::setLocale('bn');
        $this->assertSame('৳১২,৫০০', format_money(12500));
        $this->assertSame('১২ মার্চ, ২০২৬', format_date('2026-03-12'));
        $this->assertSame('৩', to_locale_digits('3'));
        $this->assertSame('১,২৩৪.৫', format_number(1234.5, 1));
    }

    public function test_t_helper_fills_placeholders(): void
    {
        app()->setLocale('en');
        $this->assertSame('Sylhet Tour Packages | TravelOrio', t('{name} Tour Packages | TravelOrio', ['name' => 'Sylhet']));
        app()->setLocale('bn');
        $this->assertSame('সিলেট ট্যুর প্যাকেজ | TravelOrio', t('{name} Tour Packages | TravelOrio', ['name' => __('Sylhet')]));
    }

    public function test_every_translated_string_in_the_views_exists_in_the_bangla_file(): void
    {
        $dictionary = json_decode(file_get_contents(base_path('lang/bn.json')), true, flags: JSON_THROW_ON_ERROR);
        $missing = [];

        foreach (glob(resource_path('views/{,*/}*.blade.php'), GLOB_BRACE) as $file) {
            preg_match_all("/(?:__|\\bt)\\('((?:[^'\\\\]|\\\\.)*)'/", file_get_contents($file), $matches);

            foreach ($matches[1] as $key) {
                $key = str_replace("\\'", "'", $key);

                if (! array_key_exists($key, $dictionary) && ! $this->isDynamicKey($key)) {
                    $missing[] = basename($file).': '.$key;
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)), 'Strings with no Bangla translation');
    }

    /** Keys built at runtime (e.g. __($name)) are checked by the page tests instead. */
    private function isDynamicKey(string $key): bool
    {
        return str_starts_with($key, '$');
    }

    public function test_bangla_file_has_no_numeric_keys(): void
    {
        // Laravel renumbers integer-like JSON keys when it merges translation files.
        $dictionary = json_decode(file_get_contents(base_path('lang/bn.json')), true);

        foreach (array_keys($dictionary) as $key) {
            $this->assertFalse(is_int($key) || ctype_digit((string) $key), "Numeric key {$key}");
        }
    }
}
