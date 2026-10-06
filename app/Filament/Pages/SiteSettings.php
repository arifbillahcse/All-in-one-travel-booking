<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Contact details and links used across the site. Saved values override the
 * defaults in config/travelorio.php through the site() helper.
 */
class SiteSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $title = 'Site settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.site-settings';

    /** setting key => form field (dots are not allowed in form state keys) */
    private const FIELDS = [
        'email' => 'email',
        'notify_email' => 'notify_email',
        'phone' => 'phone',
        'phone_display' => 'phone_display',
        'whatsapp' => 'whatsapp',
        'social.facebook' => 'facebook',
        'social.instagram' => 'instagram',
        'social.youtube' => 'youtube',
    ];

    /** @var array<string, mixed> */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isOwner();
    }

    public function mount(): void
    {
        // the placeholder link "#" is the default for social links, so show those fields empty
        $this->form->fill(collect(self::FIELDS)->mapWithKeys(fn (string $field, string $key) => [$field => site($key) === '#' ? null : site($key)])->all());
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Section::make('Contact')->description('Shown in the footer, the contact page and in every WhatsApp link.')->columns(2)->schema([
                Forms\Components\TextInput::make('email')->label('Public email')->email()->required(),
                Forms\Components\TextInput::make('notify_email')->label('Send new inquiries to')->email()
                    ->helperText('Leave empty to use the public email.'),
                Forms\Components\TextInput::make('phone')->label('Phone (international format)')->required()->regex('/^\+?\d{10,15}$/')
                    ->helperText('e.g. +8801779440297'),
                Forms\Components\TextInput::make('phone_display')->label('Phone as shown on the site')->required()
                    ->helperText('e.g. +880 1779-440297'),
                Forms\Components\TextInput::make('whatsapp')->label('WhatsApp number (digits only, with country code)')->required()->regex('/^\d{10,15}$/')
                    ->helperText('e.g. 8801779440297'),
            ]),
            Forms\Components\Section::make('Social links')->columns(3)->schema([
                Forms\Components\TextInput::make('facebook')->url()->placeholder('https://facebook.com/...'),
                Forms\Components\TextInput::make('instagram')->url()->placeholder('https://instagram.com/...'),
                Forms\Components\TextInput::make('youtube')->url()->placeholder('https://youtube.com/...'),
            ]),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach (self::FIELDS as $key => $field) {
            $value = $state[$field] ?? null;
            // an empty field goes back to the default in config/travelorio.php
            $value === null || $value === '' ? Setting::where('key', $key)->delete() : Setting::put($key, $value);
        }

        Notification::make()->title('Settings saved')->success()->send();
    }
}
