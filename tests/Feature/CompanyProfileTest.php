<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Review;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_profile_shows_the_public_offer_and_is_hidden_from_search(): void
    {
        $response = $this->get('/company-profile')->assertOk();

        $response->assertSee('Company Profile')
            ->assertSee('Weekend Escape')
            ->assertSee('BDT 12,500')
            ->assertSee('Sundarbans')
            ->assertSee('noindex, nofollow', false);
    }

    public function test_the_profile_stays_english_even_for_a_bangla_visitor(): void
    {
        $this->withHeaders(['Accept-Language' => 'bn'])->get('/company-profile')->assertOk()->assertSee('Executive Profile');
    }

    public function test_the_profile_never_shows_traveller_contact_details(): void
    {
        $lead = Inquiry::factory()->create();

        $this->get('/company-profile')->assertOk()
            ->assertDontSee($lead->phone)
            ->assertDontSee($lead->email ?? 'no-email-set@invalid');
    }

    public function test_unpublished_content_is_left_out(): void
    {
        Destination::where('slug', 'kuakata')->update(['is_published' => false]);

        $this->get('/company-profile')->assertOk()->assertDontSee('Kuakata');
    }

    public function test_only_the_first_name_of_a_reviewer_is_printed(): void
    {
        $review = Review::approved()->first();
        $last = collect(explode(' ', $review->name))->last();

        if (count(explode(' ', $review->name)) > 1) {
            $this->get('/company-profile')->assertOk()->assertDontSee($last);
        } else {
            $this->markTestSkipped('Seed reviewer has a single name.');
        }
    }
}
