<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_render_with_shared_layout(): void
    {
        foreach (['/', '/packages', '/why-us', '/reviews', '/blog', '/blog/cox-bazar-3-days', '/contact', '/destinations/sylhet'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('TravelOrio')
                ->assertSee('whatsapp-float', false)
                ->assertSee('wa.me/'.preg_replace('/\D+/', '', site('whatsapp')), false);
        }
    }

    public function test_unknown_destination_is_404(): void
    {
        $this->get('/destinations/atlantis')->assertNotFound();
    }

    public function test_unknown_blog_post_is_404(): void
    {
        $this->get('/blog/not-a-post')->assertNotFound();
    }

    public function test_destination_page_has_its_own_title(): void
    {
        $this->get('/destinations/sylhet')->assertSee('<title>Sylhet Tour Packages | TravelOrio</title>', false);
    }

    public function test_menu_links_every_page(): void
    {
        $html = $this->get('/')->getContent();

        foreach (['packages', 'why-us', 'reviews', 'blog', 'contact'] as $path) {
            $this->assertStringContainsString(url($path), $html);
        }
    }
}
