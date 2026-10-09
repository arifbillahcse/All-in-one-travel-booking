<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyRedirectTest extends TestCase
{
    public function test_old_static_pages_redirect_permanently_to_the_new_addresses(): void
    {
        foreach ([
            '/index.html' => '/',
            '/packages.html' => '/packages',
            '/reviews.html' => '/reviews',
            '/contact.html' => '/contact',
            '/why-us.html' => '/why-us',
            '/blog.html' => '/blog',
        ] as $old => $new) {
            $this->get($old)->assertStatus(301)->assertRedirect(url($new));
        }
    }

    public function test_destinations_and_articles_keep_their_slug(): void
    {
        $this->get('/destination.html?place=sylhet')->assertStatus(301)->assertRedirect(url('/destinations/sylhet'));
        $this->get('/blog-post.html?post=cox-bazar-3-days')->assertStatus(301)->assertRedirect(url('/blog/cox-bazar-3-days'));
        $this->get('/destination.html')->assertStatus(301)->assertRedirect(url('/'));
        $this->get('/blog-post.html')->assertStatus(301)->assertRedirect(url('/blog'));
    }

    public function test_the_old_language_parameter_becomes_the_bangla_address(): void
    {
        $this->get('/index.html?lang=bn')->assertStatus(301)->assertRedirect(url('/bn'));
        $this->get('/destination.html?place=kuakata&lang=bn')->assertStatus(301)->assertRedirect(url('/bn/destinations/kuakata'));
        $this->get('/blog-post.html?post=sylhet-tea-and-food&lang=bn')->assertStatus(301)->assertRedirect(url('/bn/blog/sylhet-tea-and-food'));
        $this->get('/packages.html?lang=en')->assertStatus(301)->assertRedirect(url('/packages'));
    }

    public function test_booking_prefill_parameters_survive(): void
    {
        $this->get('/packages.html?plan=Explorer&place=sylhet&guests=4&lang=bn')
            ->assertStatus(301)
            ->assertRedirect(url('/bn/packages').'?plan=Explorer&place=sylhet&guests=4');
    }

    public function test_other_html_files_are_not_redirected(): void
    {
        $this->get('/secret.html')->assertNotFound();
        $this->get('/admin.html')->assertNotFound();
    }
}
