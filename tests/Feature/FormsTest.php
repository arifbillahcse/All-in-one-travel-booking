<?php

namespace Tests\Feature;

use App\Mail\InquiryReceived;
use App\Mail\ReviewSubmitted;
use App\Models\Inquiry;
use App\Models\Review;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Mail::fake();
        RateLimiter::clear('inquiries');
    }

    private function booking(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Arif Billah',
            'phone' => '+880 1711-000000',
            'destination' => 'Sylhet',
            'package' => 'Explorer',
            'date' => now()->addMonth()->toDateString(),
            'guests' => 4,
            'message' => 'Vegetarian meals please',
        ], $overrides);
    }

    public function test_booking_is_saved_mailed_and_sent_to_whatsapp(): void
    {
        $response = $this->postJson('/inquiries/booking', $this->booking());

        $response->assertOk()->assertJsonStructure(['whatsapp_url']);

        $inquiry = Inquiry::first();
        $this->assertSame('booking', $inquiry->type);
        $this->assertSame('new', $inquiry->status);
        $this->assertSame('+8801711000000', $inquiry->phone);          // spaces and dashes removed
        $this->assertSame('sylhet', $inquiry->destination->slug);
        $this->assertSame('explorer', $inquiry->package->slug);
        $this->assertSame(50000, $inquiry->estimated_total);           // 4 x 12,500, computed on the server
        $this->assertSame('en', $inquiry->locale);

        Mail::assertSent(InquiryReceived::class, fn ($mail) => $mail->inquiry->is($inquiry) && $mail->hasTo('hello@travelorio.com'));

        $url = urldecode($response->json('whatsapp_url'));
        $this->assertStringStartsWith('https://wa.me/8801779440297?text=Hello TravelOrio! I\'d like to book a trip.', $url);
        $this->assertStringContainsString('Destination: Sylhet', $url);
        $this->assertStringContainsString('Estimated total: ৳50,000 (4 × ৳12,500)', $url);
        $this->assertStringContainsString('Message: Vegetarian meals please', $url);
    }

    public function test_bangla_booking_gets_a_bangla_message_and_is_marked_bangla(): void
    {
        $response = $this->postJson('/bn/inquiries/booking', $this->booking());

        $this->assertSame('bn', Inquiry::first()->locale);
        $url = urldecode($response->json('whatsapp_url'));
        $this->assertStringContainsString('গন্তব্য: সিলেট', $url);
        $this->assertStringContainsString('আনুমানিক মোট: ৳৫০,০০০ (৪ × ৳১২,৫০০)', $url);
    }

    public function test_notification_email_goes_to_the_configured_address(): void
    {
        config(['travelorio.notify_email' => 'owner@example.com']);

        $this->postJson('/inquiries/booking', $this->booking())->assertOk();

        Mail::assertSent(InquiryReceived::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    }

    public function test_booking_without_a_package_has_no_estimate(): void
    {
        $this->postJson('/inquiries/booking', $this->booking(['package' => '']))->assertOk();

        $this->assertNull(Inquiry::first()->package_id);
        $this->assertNull(Inquiry::first()->estimated_total);
    }

    public function test_booking_validation_errors_are_translated(): void
    {
        $this->postJson('/inquiries/booking', ['guests' => 99, 'phone' => 'abc', 'date' => '2001-01-01'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'destination', 'date', 'guests'])
            ->assertJsonPath('errors.name.0', 'Please enter your name.')
            ->assertJsonPath('errors.phone.0', 'Enter a valid number, e.g. +8801XXXXXXXXX.')
            ->assertJsonPath('errors.date.0', "Travel date can't be in the past.")
            ->assertJsonPath('errors.guests.0', 'Choose between 1 and 50 travelers.');

        $this->postJson('/bn/inquiries/booking', [])
            ->assertStatus(422)
            ->assertJsonPath('errors.name.0', 'অনুগ্রহ করে আপনার নাম লিখুন।');

        $this->assertSame(0, Inquiry::count());
        Mail::assertNothingSent();
    }

    public function test_unknown_destination_or_package_is_rejected(): void
    {
        $this->postJson('/inquiries/booking', $this->booking(['destination' => 'Atlantis']))->assertJsonValidationErrors('destination');
        $this->postJson('/inquiries/booking', $this->booking(['package' => 'Free Trip']))->assertJsonValidationErrors('package');
    }

    public function test_the_client_cannot_set_the_price(): void
    {
        $this->postJson('/inquiries/booking', $this->booking(['estimated_total' => 1, 'estimate' => '৳1']))->assertOk();

        $this->assertSame(50000, Inquiry::first()->estimated_total);
    }

    public function test_honeypot_hides_spam_without_saving_it(): void
    {
        $response = $this->postJson('/inquiries/booking', $this->booking(['website' => 'http://spam.example']));

        $response->assertOk()->assertJsonStructure(['whatsapp_url']);
        $this->assertSame(0, Inquiry::count());
        Mail::assertNothingSent();
    }

    public function test_form_without_javascript_redirects_to_whatsapp_or_back_with_errors(): void
    {
        $this->from('/packages')->post('/inquiries/booking', $this->booking())->assertRedirectContains('https://wa.me/8801779440297?text=');

        $this->from('/packages')->post('/inquiries/booking', [])
            ->assertRedirect('/packages')
            ->assertSessionHasErrors(['name', 'phone']);
    }

    public function test_pages_with_forms_carry_csrf_tokens_and_a_honeypot(): void
    {
        foreach (['/', '/packages', '/contact', '/reviews', '/destinations/sylhet'] as $url) {
            $html = $this->get($url)->getContent();
            $this->assertStringContainsString('<meta name="csrf-token"', $html, $url);
            $this->assertStringContainsString('name="_token"', $html, $url);
            $this->assertStringContainsString('name="website"', $html, $url);
        }
    }

    public function test_server_errors_and_old_input_are_shown_without_javascript(): void
    {
        $this->from('/contact')->post('/inquiries/contact', ['name' => 'Arif', 'message' => 'short']);

        $this->get('/contact')
            ->assertSee('Please enter your phone number.')
            ->assertSee('Please write at least 10 characters.')
            ->assertSee('value="Arif"', false);
    }

    public function test_contact_message_is_saved_and_sent_to_whatsapp(): void
    {
        $response = $this->postJson('/inquiries/contact', [
            'name' => 'Nusrat', 'phone' => '01711000000', 'email' => 'n@example.com',
            'topic' => 'Group or corporate tour', 'message' => 'We are 12 colleagues.',
        ])->assertOk();

        $inquiry = Inquiry::first();
        $this->assertSame('contact', $inquiry->type);
        $this->assertSame('Group or corporate tour', $inquiry->topic);
        $url = urldecode($response->json('whatsapp_url'));
        $this->assertStringContainsString('Hello TravelOrio! I have a question.', $url);
        $this->assertStringContainsString('Email: n@example.com', $url);
        Mail::assertSent(InquiryReceived::class);
    }

    public function test_contact_validation(): void
    {
        $this->postJson('/inquiries/contact', ['name' => 'N', 'phone' => '1', 'email' => 'nope', 'topic' => 'x', 'message' => 'short'])
            ->assertJsonValidationErrors(['name', 'phone', 'email', 'topic', 'message']);
    }

    public function test_submitted_review_waits_for_approval(): void
    {
        $response = $this->postJson('/reviews', [
            'name' => 'Farhan', 'destination' => 'Kuakata', 'rating' => 5,
            'text' => 'A lovely trip with a very helpful guide.',
        ])->assertOk();

        $review = Review::where('name->en', 'Farhan')->first();
        $this->assertFalse($review->is_approved);
        $this->assertSame('kuakata', $review->destination->slug);
        Mail::assertSent(ReviewSubmitted::class);
        $this->assertStringContainsString('New review for TravelOrio (please moderate):', urldecode($response->json('whatsapp_url')));

        $this->get('/reviews?show=60')->assertDontSee('Farhan');
    }

    public function test_review_validation(): void
    {
        $this->postJson('/reviews', ['name' => '', 'destination' => 'x', 'rating' => 9, 'text' => 'short'])
            ->assertJsonValidationErrors(['name', 'destination', 'rating', 'text']);
    }

    public function test_too_many_requests_are_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/inquiries/booking', $this->booking())->assertOk();
        }

        $this->postJson('/inquiries/booking', $this->booking())->assertStatus(429);
    }

    public function test_a_mail_failure_does_not_stop_the_visitor(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->postJson('/inquiries/booking', $this->booking())->assertOk()->assertJsonStructure(['whatsapp_url']);
        $this->assertSame(1, Inquiry::count());
    }

    public function test_error_pages_use_the_site_layout_in_both_languages(): void
    {
        $this->get('/nowhere')->assertNotFound()->assertSee('Back to home');
        $this->get('/bn/nowhere')->assertNotFound()->assertSee('হোমে ফিরে যান');
    }
}
