<?php

namespace Tests\Feature;

use Tests\TestCase;

class PagesTest extends TestCase
{
    public function test_public_pages_render_with_shared_layout(): void
    {
        foreach (['/', '/packages', '/why-us', '/reviews', '/blog', '/blog/sample', '/contact', '/destinations/sylhet'] as $url) {
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

    public function test_menu_links_every_page(): void
    {
        $html = $this->get('/')->getContent();

        foreach (['packages', 'why-us', 'reviews', 'blog', 'contact'] as $path) {
            $this->assertStringContainsString(url($path), $html);
        }
    }
}
