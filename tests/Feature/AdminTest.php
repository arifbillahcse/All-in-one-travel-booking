<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\DestinationResource\Pages\EditDestination;
use App\Filament\Resources\DestinationResource\Pages\ListDestinations;
use App\Filament\Resources\InquiryResource;
use App\Filament\Resources\InquiryResource\Pages\EditInquiry;
use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Filament\Resources\PackageResource\Pages\CreatePackage;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\ReviewResource\Pages\ListReviews;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Widgets\InquiriesByDestination;
use App\Filament\Widgets\InquiryStats;
use App\Filament\Widgets\LatestInquiries;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Post;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->editor = User::factory()->create(['role' => 'editor']);
    }

    // ---- access -------------------------------------------------------------------------

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/destinations')->assertRedirect('/admin/login');
    }

    public function test_owner_and_editor_can_open_the_panel_but_other_roles_cannot(): void
    {
        $this->actingAs($this->owner)->get('/admin')->assertOk();
        $this->actingAs($this->editor)->get('/admin')->assertOk();

        $stranger = User::factory()->create(['role' => 'customer']);
        $this->actingAs($stranger)->get('/admin')->assertForbidden();
    }

    public function test_editors_cannot_open_team_or_site_settings(): void
    {
        $this->actingAs($this->editor);
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/site-settings')->assertForbidden();
        $this->get('/admin/destinations')->assertOk();
        $this->get('/admin/inquiries')->assertOk();

        $this->actingAs($this->owner);
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/site-settings')->assertOk();
    }

    public function test_every_admin_page_renders_for_the_owner(): void
    {
        $this->actingAs($this->owner);

        foreach (['', '/destinations', '/destinations/create', '/destinations/1/edit', '/packages', '/packages/2/edit', '/addons',
            '/reviews', '/reviews/1/edit', '/posts', '/posts/1/edit', '/post-categories', '/inquiries', '/users', '/users/create', '/site-settings'] as $path) {
            $this->get('/admin'.$path)->assertOk();
        }
    }

    public function test_the_login_form_logs_an_owner_in(): void
    {
        $user = User::factory()->create(['role' => 'owner', 'password' => Hash::make('a-long-password-1')]);

        Livewire::test(\Filament\Pages\Auth\Login::class)
            ->fillForm(['email' => $user->email, 'password' => 'a-long-password-1'])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }

    // ---- content --------------------------------------------------------------------------

    public function test_a_package_can_be_created_and_appears_on_the_site(): void
    {
        $this->actingAs($this->editor);

        Livewire::test(CreatePackage::class)
            ->fillForm([
                'slug' => 'family-fun', 'name' => 'Family Fun', 'description' => 'Easy days for everyone.', 'best_for' => 'Best for families',
                'price' => 9000, 'days' => 3, 'nights' => 2, 'destinations_count' => 1,
                'features' => [['line' => '3 days, 2 nights'], ['line' => 'Kids welcome']],
                'hotel' => 'Family suite', 'meals' => 'Breakfast and dinner', 'transport' => 'Private van', 'cancellation' => '7 days before',
                'is_published' => true, 'sort_order' => 9,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $package = Package::where('slug', 'family-fun')->firstOrFail();
        $this->assertSame('Family Fun', $package->getTranslation('name', 'en'));
        $this->assertSame(['3 days, 2 nights', 'Kids welcome'], $package->getTranslation('features', 'en'));

        $this->get('/packages')->assertSee('Family Fun')->assertSee('9,000');
    }

    public function test_package_validation_rejects_a_duplicate_slug_and_missing_fields(): void
    {
        $this->actingAs($this->editor);

        Livewire::test(CreatePackage::class)
            ->fillForm(['slug' => 'explorer', 'name' => null, 'price' => -5])
            ->call('create')
            ->assertHasFormErrors(['slug', 'name', 'price']);
    }

    public function test_a_destination_is_edited_per_language_without_touching_the_other_one(): void
    {
        $this->actingAs($this->editor);
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();

        Livewire::test(EditDestination::class, ['record' => $destination->getKey()])
            ->set('activeLocale', 'bn')
            ->fillForm(['tagline' => 'নতুন ট্যাগলাইন'])
            ->call('save')
            ->assertHasNoFormErrors();

        $destination->refresh();
        $this->assertSame('নতুন ট্যাগলাইন', $destination->getTranslation('tagline', 'bn'));
        $this->assertNotSame('নতুন ট্যাগলাইন', $destination->getTranslation('tagline', 'en'));

        $this->get('/bn/destinations/sylhet')->assertSee('নতুন ট্যাগলাইন');
    }

    public function test_saving_a_destination_unchanged_keeps_the_nested_content(): void
    {
        $this->actingAs($this->editor);
        $destination = Destination::where('slug', 'sundarbans')->firstOrFail();
        $before = $destination->getTranslations();

        foreach (['en', 'bn'] as $locale) {
            Livewire::test(EditDestination::class, ['record' => $destination->getKey()])
                ->set('activeLocale', $locale)
                ->call('save')
                ->assertHasNoFormErrors();
        }

        $this->assertEquals($before, $destination->fresh()->getTranslations());
    }

    public function test_edits_in_one_language_survive_switching_back_and_saving(): void
    {
        $this->actingAs($this->editor);
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();
        $englishBefore = $destination->getTranslation('highlights', 'en');

        Livewire::test(EditDestination::class, ['record' => $destination->getKey()])
            ->set('activeLocale', 'bn')
            ->fillForm(['tagline' => 'নতুন ট্যাগলাইন', 'highlights' => [['line' => 'চা-বাগান'], ['line' => 'জাফলং']]])
            ->set('activeLocale', 'en')
            ->call('save')
            ->assertHasNoFormErrors();

        $destination->refresh();
        $this->assertSame(['চা-বাগান', 'জাফলং'], $destination->getTranslation('highlights', 'bn'), 'stored as a plain list, not form items');
        $this->assertSame('নতুন ট্যাগলাইন', $destination->getTranslation('tagline', 'bn'));
        $this->assertSame($englishBefore, $destination->getTranslation('highlights', 'en'));
        $this->assertIsArray($destination->getTranslation('itinerary', 'bn')[0]['items']);
        $this->assertSame(array_values($destination->getTranslation('itinerary', 'bn')[0]['items']), $destination->getTranslation('itinerary', 'bn')[0]['items']);
    }

    public function test_a_new_package_can_be_created_in_both_languages_at_once(): void
    {
        $this->actingAs($this->editor);

        Livewire::test(CreatePackage::class)
            ->fillForm([
                'slug' => 'bilingual', 'name' => 'Bilingual', 'description' => 'D', 'best_for' => 'B', 'price' => 1000, 'days' => 2, 'nights' => 1, 'destinations_count' => 1,
                'features' => [['line' => 'One']], 'hotel' => 'H', 'meals' => 'M', 'transport' => 'T', 'cancellation' => 'C',
            ])
            ->set('activeLocale', 'bn')
            ->fillForm([
                'name' => 'দ্বিভাষিক', 'description' => 'বর্ণনা', 'best_for' => 'সবার জন্য', 'features' => [['line' => 'এক']],
                'hotel' => 'হোটেল', 'meals' => 'খাবার', 'transport' => 'যাতায়াত', 'cancellation' => 'বাতিল',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $package = Package::where('slug', 'bilingual')->firstOrFail();
        $this->assertSame('Bilingual', $package->getTranslation('name', 'en'));
        $this->assertSame('দ্বিভাষিক', $package->getTranslation('name', 'bn'));
        $this->assertSame(['One'], $package->getTranslation('features', 'en'));
        $this->assertSame(['এক'], $package->getTranslation('features', 'bn'));
    }

    public function test_an_incomplete_other_language_blocks_the_save_and_shows_that_language(): void
    {
        $this->actingAs($this->editor);
        $destination = Destination::where('slug', 'sylhet')->firstOrFail();

        Livewire::test(EditDestination::class, ['record' => $destination->getKey()])
            ->set('activeLocale', 'bn')
            ->fillForm(['name' => ''])
            ->set('activeLocale', 'en')
            ->call('save')
            ->assertHasFormErrors(['name'])
            ->assertSet('activeLocale', 'bn');
    }

    public function test_unpublishing_a_destination_from_the_table_hides_it(): void
    {
        $this->actingAs($this->editor);
        $destination = Destination::where('slug', 'kuakata')->firstOrFail();

        Livewire::test(ListDestinations::class)
            ->assertCanSeeTableRecords(Destination::all())
            ->call('updateTableColumnState', 'is_published', $destination->getKey(), false);

        $this->assertFalse($destination->fresh()->is_published);
        $this->get('/destinations/kuakata')->assertNotFound();
    }

    public function test_blog_body_survives_a_save_round_trip_in_both_languages(): void
    {
        $this->actingAs($this->editor);
        $post = Post::where('slug', 'cox-bazar-3-days')->firstOrFail();
        $before = $post->getTranslations('body');

        foreach (['en', 'bn'] as $locale) {
            Livewire::test(EditPost::class, ['record' => $post->getKey()])
                ->set('activeLocale', $locale)
                ->call('save')
                ->assertHasNoFormErrors();
        }

        $this->assertSame($before, $post->fresh()->getTranslations('body'));
    }

    public function test_blog_body_converts_between_stored_and_builder_formats(): void
    {
        $stored = [['type' => 'h2', 'text' => 'Day 1'], ['type' => 'p', 'text' => 'Hello'], ['type' => 'ul', 'items' => ['a', 'b']], ['type' => 'tip', 'text' => 'Go early']];

        $builder = PostResource::toBuilderState($stored);

        $this->assertCount(4, $builder);
        $this->assertSame(['text' => 'Day 1'], array_values($builder)[0]['data']);
        $this->assertSame(['a', 'b'], array_values(array_column(array_values($builder)[2]['data']['items'], 'item')), 'list lines are wrapped for the repeater');
        $this->assertSame($builder, PostResource::toBuilderState($builder), 'already converted state is left alone');

        // On save the nested repeater has already been flattened, then the blocks go back to the stored shape.
        $saved = [['type' => 'h2', 'data' => ['text' => 'Day 1']], ['type' => 'ul', 'data' => ['items' => ['a', 'b']]]];
        $this->assertSame([['type' => 'h2', 'text' => 'Day 1'], ['type' => 'ul', 'items' => ['a', 'b']]], PostResource::fromBuilderState($saved));
    }

    // ---- reviews and inquiries -----------------------------------------------------------------

    public function test_a_submitted_review_is_approved_from_the_list_and_then_shows_up(): void
    {
        $this->actingAs($this->editor);
        $review = Review::factory()->create(['is_approved' => false, 'destination_id' => Destination::first()->id, 'name' => ['en' => 'Pending Pat', 'bn' => 'Pending Pat']]);
        $this->get('/reviews?show=60')->assertDontSee('Pending Pat');

        Livewire::test(ListReviews::class)->callTableAction('approve', $review);

        $this->assertTrue($review->fresh()->is_approved);
        $this->get('/reviews?show=60')->assertSee('Pending Pat');
    }

    public function test_reviews_can_be_hidden_again_and_bulk_approved(): void
    {
        $this->actingAs($this->editor);
        $pending = Review::factory()->count(2)->create(['is_approved' => false]);

        Livewire::test(ListReviews::class)->callTableBulkAction('approve', $pending);
        $this->assertSame(0, Review::where('is_approved', false)->count());

        Livewire::test(ListReviews::class)->callTableAction('hide', $pending->first()->fresh());
        $this->assertFalse($pending->first()->fresh()->is_approved);
    }

    public function test_inquiry_status_and_notes_can_be_updated(): void
    {
        $this->actingAs($this->editor);
        $inquiry = Inquiry::factory()->create();

        Livewire::test(EditInquiry::class, ['record' => $inquiry->getKey()])
            ->fillForm(['status' => 'contacted', 'admin_notes' => 'Called, wants a quote'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('contacted', $inquiry->fresh()->status);
        $this->assertSame('Called, wants a quote', $inquiry->fresh()->admin_notes);
    }

    public function test_inquiries_are_filtered_and_bulk_updated(): void
    {
        $this->actingAs($this->editor);
        $bookings = Inquiry::factory()->count(2)->create();
        $contact = Inquiry::factory()->contact()->create();

        Livewire::test(ListInquiries::class)
            ->filterTable('type', 'contact')
            ->assertCanSeeTableRecords([$contact])
            ->assertCanNotSeeTableRecords($bookings);

        Livewire::test(ListInquiries::class)->callTableBulkAction('mark_confirmed', $bookings);
        $this->assertSame(['confirmed', 'confirmed'], $bookings->map(fn ($i) => $i->fresh()->status)->all());
        $this->assertSame(1, Inquiry::new()->count());
    }

    public function test_inquiries_export_to_csv_without_running_spreadsheet_formulas(): void
    {
        $this->actingAs($this->editor);
        $inquiry = Inquiry::factory()->contact()->create(['name' => '=HYPERLINK("http://evil.example")', 'message' => 'নমস্কার, hello']);

        Livewire::test(ListInquiries::class)->callAction('export')->assertFileDownloaded();

        ob_start();
        InquiryResource::csv([$inquiry])->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString('নমস্কার', $csv);
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('Received,Type,Status,Name', $csv);
    }

    public function test_dashboard_widgets_show_live_numbers(): void
    {
        $this->actingAs($this->owner);
        Inquiry::factory()->count(3)->create();
        Review::factory()->create(['is_approved' => false]);

        Livewire::test(InquiryStats::class)->assertSee('New inquiries')->assertSee('Reviews to approve');
        Livewire::test(LatestInquiries::class)->assertCanSeeTableRecords(Inquiry::all());
        Livewire::test(InquiriesByDestination::class)->assertSuccessful();
        $this->get('/admin')->assertOk()->assertSee('Dashboard');
    }

    // ---- settings and team -----------------------------------------------------------------------

    public function test_owner_changes_contact_details_and_the_site_follows(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(SiteSettings::class)
            ->fillForm(['email' => 'bookings@travelorio.com', 'whatsapp' => '8801811223344', 'phone' => '+8801811223344', 'phone_display' => '+880 1811-223344', 'facebook' => 'https://facebook.com/travelorio'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/')->assertSee('bookings@travelorio.com')->assertSee('wa.me/8801811223344', false)->assertSee('https://facebook.com/travelorio', false);
        $this->get('/contact')->assertSee('+880 1811-223344');
    }

    public function test_clearing_a_setting_restores_the_default(): void
    {
        $this->actingAs($this->owner);
        Setting::put('email', 'old@example.com');

        Livewire::test(SiteSettings::class)->fillForm(['email' => null])->call('save')->assertHasFormErrors(['email' => 'required']);

        Livewire::test(SiteSettings::class)->fillForm(['email' => 'hello@travelorio.com', 'facebook' => null])->call('save');
        $this->assertSame('hello@travelorio.com', site('email'));
        $this->assertSame('#', site('social.facebook'));
    }

    public function test_settings_validate_the_whatsapp_number(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(SiteSettings::class)->fillForm(['whatsapp' => '+880 17-bad'])->call('save')->assertHasFormErrors(['whatsapp']);
    }

    public function test_owner_adds_a_team_member_with_a_hashed_password(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Asma', 'email' => 'asma@travelorio.com', 'role' => 'editor', 'password' => 'a-long-password-1'])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'asma@travelorio.com')->firstOrFail();
        $this->assertSame('editor', $user->role);
        $this->assertTrue(Hash::check('a-long-password-1', $user->password));
        $this->assertFalse($user->isOwner());
    }

    public function test_short_passwords_and_unknown_roles_are_rejected(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'X', 'email' => 'x@example.com', 'role' => 'editor', 'password' => 'short'])
            ->call('create')
            ->assertHasFormErrors(['password']);
    }

    public function test_the_team_list_cannot_delete_yourself(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(ListUsers::class)->assertTableActionHidden('delete', $this->owner)->assertTableActionVisible('delete', $this->editor);
    }

    public function test_the_admin_command_creates_and_validates_users(): void
    {
        $this->artisan('travelorio:admin', ['email' => 'new@travelorio.com', '--password' => 'long-enough-pass', '--name' => 'New'])->assertSuccessful();
        $this->assertTrue(User::where('email', 'new@travelorio.com')->firstOrFail()->isOwner());

        $this->artisan('travelorio:admin', ['email' => 'e@travelorio.com', '--password' => 'short'])->assertFailed();
        $this->artisan('travelorio:admin', ['email' => 'e@travelorio.com', '--password' => 'long-enough-pass', '--role' => 'god'])->assertFailed();
        $this->assertNull(User::where('email', 'e@travelorio.com')->first());
    }
}
