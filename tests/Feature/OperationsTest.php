<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\User;
use App\Services\BackupService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class OperationsTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        // A real SQLite file: an in-memory database cannot be backed up and restored.
        $this->dir = sys_get_temp_dir().'/travelorio-ops-'.bin2hex(random_bytes(5));
        mkdir($this->dir);
        touch($this->dir.'/test.sqlite');
        config([
            'database.connections.sqlite.database' => $this->dir.'/test.sqlite',
            'travelorio.backup.path' => $this->dir.'/backups',
            'travelorio.backup.keep' => 14,
        ]);
        DB::purge();
        Storage::fake('public');

        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->seed(DatabaseSeeder::class);
    }

    protected function tearDown(): void
    {
        DB::disconnect();
        exec('rm -rf '.escapeshellarg($this->dir));
        parent::tearDown();
    }

    // ---- backups ----------------------------------------------------------------------------------------

    public function test_a_backup_contains_the_database_the_photos_and_a_checksum(): void
    {
        Storage::disk('public')->put('1/conversions/hero-640.webp', 'photo-bytes');

        $this->artisan('travelorio:backup')->assertSuccessful();

        $file = app(BackupService::class)->list()[0];
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($file));
        $manifest = json_decode($zip->getFromName('manifest.json'), true);

        $this->assertSame('sqlite', $manifest['database']);
        $this->assertSame(1, $manifest['uploads']);
        $this->assertNotFalse($zip->locateName('uploads/1/conversions/hero-640.webp'));
        $this->assertSame($manifest['database_sha256'], hash('sha256', $zip->getFromName('database.sqlite')));
        $this->assertSame('0600', substr(sprintf('%o', fileperms($file)), -4), 'only the owner can read backups');
    }

    public function test_restoring_brings_back_deleted_content_and_photos(): void
    {
        Storage::disk('public')->put('1/hero.webp', 'original-photo');
        $this->artisan('travelorio:backup')->assertSuccessful();
        $this->assertSame(6, Destination::count());

        // disaster: content deleted, photo replaced, a new lead arrives after the backup
        Destination::query()->delete();
        Storage::disk('public')->put('1/hero.webp', 'damaged');
        Storage::disk('public')->delete('1/hero.webp');
        Inquiry::factory()->create();
        $this->assertSame(0, Destination::count());

        $this->artisan('travelorio:restore', ['--force' => true])->assertSuccessful();

        $this->assertSame(6, Destination::count());
        $this->assertSame(0, Inquiry::count(), 'data written after the backup is gone, as expected');
        $this->assertSame('original-photo', Storage::disk('public')->get('1/hero.webp'));
        $this->get('/destinations/sylhet')->assertOk();
    }

    public function test_restore_first_takes_a_safety_backup_of_the_current_state(): void
    {
        $this->artisan('travelorio:backup')->assertSuccessful();
        sleep(1);   // backup names carry the second

        $this->artisan('travelorio:restore', ['--force' => true])->assertSuccessful();

        $this->assertCount(2, app(BackupService::class)->list());
    }

    public function test_a_damaged_backup_is_refused_and_nothing_is_changed(): void
    {
        $this->artisan('travelorio:backup')->assertSuccessful();
        $file = app(BackupService::class)->list()[0];

        // swap the database inside the zip for different bytes, keeping the manifest
        $zip = new ZipArchive;
        $zip->open($file);
        $zip->addFromString('database.sqlite', 'not the original');
        $zip->close();

        Destination::where('slug', 'kuakata')->delete();

        $this->artisan('travelorio:restore', ['file' => $file, '--force' => true, '--no-safety' => true])->assertFailed();
        $this->assertSame(5, Destination::count(), 'the current data is untouched');
    }

    public function test_a_backup_with_path_tricks_cannot_write_outside_the_photos_folder(): void
    {
        $this->artisan('travelorio:backup')->assertSuccessful();
        $file = app(BackupService::class)->list()[0];
        $zip = new ZipArchive;
        $zip->open($file);
        $zip->addFromString('uploads/../../evil.php', '<?php echo 1;');
        $zip->close();

        $this->artisan('travelorio:restore', ['file' => $file, '--force' => true, '--no-safety' => true])->assertFailed();
        $this->assertFileDoesNotExist(dirname(Storage::disk('public')->path('')).'/evil.php');
    }

    public function test_old_backups_are_pruned_to_the_newest_few(): void
    {
        $service = app(BackupService::class);
        foreach (['20260101-000000', '20260102-000000', '20260103-000000', '20260104-000000'] as $stamp) {
            touch($service->directory()."/travelorio-{$stamp}.zip");
        }

        $deleted = $service->prune(2);

        $this->assertCount(2, $deleted);
        $this->assertSame(['travelorio-20260104-000000.zip', 'travelorio-20260103-000000.zip'], array_map('basename', $service->list()));
    }

    public function test_a_second_copy_can_go_to_another_disk(): void
    {
        Storage::fake('offsite');
        config(['travelorio.backup.disk' => 'offsite']);

        $this->artisan('travelorio:backup')->assertSuccessful();

        $this->assertCount(1, Storage::disk('offsite')->files('travelorio-backups'));
    }

    public function test_mysql_connection_options_never_put_the_password_on_the_command_line(): void
    {
        config(['database.default' => 'mysql', 'database.connections.mysql.host' => 'db.internal', 'database.connections.mysql.port' => 3307,
            'database.connections.mysql.username' => 'travel', 'database.connections.mysql.password' => 'top-secret']);

        $arguments = implode(' ', app(BackupService::class)->mysqlArguments());

        $this->assertSame('--host=db.internal --port=3307 --user=travel', $arguments);
        $this->assertStringNotContainsString('top-secret', $arguments);
    }

    public function test_backups_run_every_night_and_failed_jobs_are_cleaned_up(): void
    {
        $commands = collect(app(Schedule::class)->events())->map(fn ($event) => $event->command)->implode("\n");

        $this->assertStringContainsString('travelorio:backup', $commands);
        $this->assertStringContainsString('queue:prune-failed', $commands);
    }

    // ---- preflight ------------------------------------------------------------------------------------------

    public function test_preflight_flags_an_unsafe_setup(): void
    {
        config(['app.debug' => true, 'app.url' => 'http://localhost', 'mail.default' => 'log']);

        $this->artisan('travelorio:preflight')
            ->expectsOutputToContain('FAIL')
            ->assertFailed();
    }

    public function test_preflight_accepts_a_correct_production_setup(): void
    {
        User::factory()->create(['role' => 'owner']);
        $this->artisan('travelorio:backup')->assertSuccessful();
        config([
            'app.debug' => false, 'app.url' => 'https://travelorio.com', 'mail.default' => 'smtp', 'queue.default' => 'database',
            'logging.channels.stack.channels' => ['daily'], 'cache.default' => 'file',
        ]);
        $this->app['env'] = 'production';

        $output = $this->artisan('travelorio:preflight');
        $output->expectsOutputToContain('APP_DEBUG is off');

        // only things that depend on this machine (ini sizes, build, links) may remain
        $failed = collect(Artisan::output())->implode('');
        $this->assertStringNotContainsString('Email is really sent (MAIL', $failed);
    }

    // ---- staging switch ---------------------------------------------------------------------------------------------

    public function test_staging_sites_hide_from_search_engines(): void
    {
        config(['travelorio.noindex' => true]);

        $this->get('/')->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertSee('<meta name="robots" content="noindex,follow">', false);
        $this->get('/robots.txt')->assertSee('Disallow: /')->assertDontSee('Sitemap:');
    }

    public function test_the_live_site_can_be_indexed(): void
    {
        $this->get('/')->assertHeaderMissing('X-Robots-Tag');
        $this->get('/robots.txt')->assertSee('Sitemap:');
    }
}
