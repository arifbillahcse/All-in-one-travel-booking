<?php

namespace Tests\Feature;

use App\Mail\InquiryReceived;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function csp(string $url = '/'): string
    {
        return (string) $this->get($url)->headers->get('Content-Security-Policy');
    }

    public function test_public_pages_send_browser_security_headers(): void
    {
        foreach (['/', '/bn/packages', '/blog/cox-bazar-3-days', '/nowhere'] as $url) {
            $response = $this->get($url);

            $response->assertHeader('X-Content-Type-Options', 'nosniff');
            $response->assertHeader('X-Frame-Options', 'DENY');
            $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
            $this->assertStringContainsString('camera=()', $response->headers->get('Permissions-Policy'));
        }
    }

    public function test_scripts_need_the_per_request_nonce_and_nothing_is_allowed_inline_or_eval(): void
    {
        $policy = $this->csp();

        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[A-Za-z0-9+\\/=]{20,}'(;|$)/", $policy);
        $this->assertStringNotContainsString("'unsafe-eval'", $policy);
        $this->assertDoesNotMatchRegularExpression("/script-src[^;]*'unsafe-inline'/", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString("base-uri 'self'", $policy);
        $this->assertStringContainsString('form-action', $policy);
    }

    public function test_inline_scripts_carry_the_nonce_and_it_changes_on_every_request(): void
    {
        $first = $this->get('/');
        preg_match("/'nonce-([^']+)'/", $first->headers->get('Content-Security-Policy'), $m);

        $html = $first->getContent();
        preg_match_all('/<script(?![^>]*\bsrc=)(?![^>]*type="application\/ld\+json")([^>]*)>/', $html, $inline);
        $this->assertNotEmpty($inline[1]);
        foreach ($inline[1] as $attributes) {
            $this->assertStringContainsString('nonce="'.$m[1].'"', $attributes, 'every inline script has the nonce');
        }
        $this->assertStringNotContainsString(' onclick=', $html);
        $this->assertStringNotContainsString(' onload=', $html);

        preg_match("/'nonce-([^']+)'/", $this->csp(), $second);
        $this->assertNotSame($m[1], $second[1]);
    }

    public function test_the_map_is_the_only_framed_site_and_forms_may_only_go_to_whatsapp(): void
    {
        $policy = $this->csp('/contact');

        $this->assertStringContainsString('frame-src https://www.openstreetmap.org', $policy);
        $this->assertStringContainsString("form-action 'self' https://wa.me https://api.whatsapp.com", $policy);
        $this->assertStringContainsString('sandbox="allow-scripts allow-same-origin allow-popups"', $this->get('/contact')->getContent());
    }

    public function test_the_admin_panel_is_not_indexed_nor_framed_by_other_sites(): void
    {
        $response = $this->get('/admin/login');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertNull($response->headers->get('Content-Security-Policy'), 'the panel needs its own inline scripts');
    }

    public function test_hsts_is_only_sent_over_https_in_production(): void
    {
        $this->assertNull($this->get('/')->headers->get('Strict-Transport-Security'));

        $this->app['env'] = 'production';
        $this->assertNull($this->get('http://localhost/')->headers->get('Strict-Transport-Security'));
        $this->assertStringContainsString('max-age=31536000', (string) $this->get('https://localhost/')->headers->get('Strict-Transport-Security'));
        $this->assertStringContainsString('upgrade-insecure-requests', (string) $this->get('https://localhost/')->headers->get('Content-Security-Policy'));
    }

    public function test_session_cookies_are_not_readable_by_scripts_and_not_sent_cross_site(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertSame('lax', config('session.same_site'));
    }

    // ---- stored content ---------------------------------------------------------------------------

    public function test_rich_text_keeps_emphasis_and_nothing_else(): void
    {
        $this->assertSame('<strong>By air:</strong> 1 hour', (string) rich_text('<strong>By air:</strong> 1 hour'));
        $this->assertSame('<strong>x</strong>', (string) rich_text('<strong onmouseover="alert(1)" style="x">x</strong>'));
        $this->assertSame('hi alert(1)', (string) rich_text('hi <script>alert(1)</script>'));
        $this->assertSame('', (string) rich_text('<img src=x onerror=alert(1)>'));
        $this->assertSame('<em>fine</em>', (string) rich_text('<a href="javascript:alert(1)"><em>fine</em></a>'));
    }

    public function test_html_written_in_the_admin_cannot_run_scripts_on_the_public_page(): void
    {
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();
        $destination->setTranslation('transport', 'en', ['<strong onmouseover="alert(1)">By air</strong><script>alert(2)</script><img src=x onerror=alert(3)>']);
        $destination->setTranslation('name', 'en', 'Sylhet <script>alert(4)</script>');
        $destination->save();

        $html = $this->get('/destinations/sylhet')->getContent();

        $this->assertStringContainsString('<strong>By air</strong>', $html);
        foreach (['onmouseover', 'onerror', '<script>alert'] as $bad) {
            $this->assertStringNotContainsString($bad, $html, $bad);
        }
        $this->assertStringContainsString('Sylhet &lt;script&gt;alert(4)&lt;/script&gt;', $html);
    }

    public function test_visitor_text_is_escaped_where_it_is_shown_back(): void
    {
        $this->post('/contact-not-a-route', []);   // unrelated 404 must not echo input
        $this->from('/contact')->post('/inquiries/contact', ['name' => '"><script>alert(1)</script>', 'message' => 'short']);

        $html = $this->get('/contact')->getContent();
        $this->assertStringNotContainsString('<script>alert(1)', $html);
        $this->assertStringContainsString('&quot;&gt;&lt;script&gt;alert(1)', $html);
    }

    // ---- forms ----------------------------------------------------------------------------------------

    private function booking(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Arif', 'phone' => '+8801711000000', 'destination' => 'Sylhet', 'package' => 'Explorer',
            'date' => now()->addMonth()->toDateString(), 'guests' => 2,
        ], $overrides);
    }

    public function test_line_breaks_in_a_name_cannot_inject_email_headers(): void
    {
        Mail::fake();

        $this->postJson('/inquiries/booking', $this->booking(['name' => "Evil\r\nBcc: attacker@example.com"]))->assertOk();

        $this->assertSame('Evil Bcc: attacker@example.com', Inquiry::first()->name);
        Mail::assertSent(InquiryReceived::class, function (InquiryReceived $mail) {
            $subject = $mail->envelope()->subject;

            return ! preg_match('/[\r\n]/', $subject) && ! $mail->hasBcc('attacker@example.com');
        });
    }

    public function test_extra_fields_cannot_change_status_type_or_price(): void
    {
        Mail::fake();

        $this->postJson('/inquiries/booking', $this->booking(['status' => 'confirmed', 'type' => 'contact', 'estimated_total' => 1, 'id' => 999, 'admin_notes' => 'x']))->assertOk();

        $inquiry = Inquiry::first();
        $this->assertSame(['new', 'booking', 25000, null], [$inquiry->status, $inquiry->type, $inquiry->estimated_total, $inquiry->admin_notes]);
        $this->assertNotSame(999, $inquiry->id);
    }

    public function test_oversized_messages_are_rejected(): void
    {
        $this->postJson('/inquiries/booking', $this->booking(['message' => str_repeat('a', 1001)]))->assertJsonValidationErrors('message');
        $this->postJson('/inquiries/contact', ['name' => 'A B', 'phone' => '01711000000', 'topic' => 'Partnership', 'message' => str_repeat('a', 2001)])->assertJsonValidationErrors('message');
    }

    // ---- admin ----------------------------------------------------------------------------------------------

    public function test_repeated_wrong_passwords_lock_the_login_form(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $user = User::factory()->create(['role' => 'owner', 'password' => Hash::make('a-long-password-1')]);

        for ($i = 0; $i < 6; $i++) {
            Livewire::test(Login::class)->fillForm(['email' => $user->email, 'password' => 'wrong-'.$i])->call('authenticate');
        }

        Livewire::test(Login::class)->fillForm(['email' => $user->email, 'password' => 'a-long-password-1'])->call('authenticate');
        $this->assertGuest();
    }

    public function test_admin_pages_cannot_be_reached_without_signing_in(): void
    {
        foreach (['/admin/destinations', '/admin/inquiries', '/admin/users', '/admin/site-settings', '/admin/posts/1/edit'] as $url) {
            $this->get($url)->assertRedirect('/admin/login');
        }
    }

    public function test_private_files_and_secrets_are_not_served_by_the_public_folder(): void
    {
        foreach (['/.env', '/composer.json', '/storage/logs/laravel.log', '/database/database.sqlite'] as $url) {
            $this->assertContains($this->get($url)->status(), [403, 404], $url);
        }
        $this->assertFileDoesNotExist(public_path('.env'));
        $this->assertFileDoesNotExist(public_path('phpinfo.php'));
    }
}
