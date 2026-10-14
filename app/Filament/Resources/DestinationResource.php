<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinationResource\Pages;
use App\Models\Destination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DestinationResource extends Resource
{
    use Translatable;

    /** Admin URLs use the record id, not the public slug (slugs can be edited). */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Destination::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    /** Fields shown as a list of text lines. */
    private static function lines(string $name, string $label, bool $long = false): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->simple($long ? Forms\Components\Textarea::make('line')->rows(3)->required() : Forms\Components\TextInput::make('line')->required())
            ->addActionLabel('Add')
            ->reorderable()
            ->collapsible();
    }

    private static function photo(string $collection, string $label): Forms\Components\SpatieMediaLibraryFileUpload
    {
        return Forms\Components\SpatieMediaLibraryFileUpload::make($collection)->label($label)->collection($collection)
            ->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240)
            ->helperText('JPG, PNG or WebP, up to 10 MB. It is resized automatically. Empty shows the placeholder.');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Forms\Components\Tabs\Tab::make('Basics')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->alphaDash()
                            ->helperText('Used in the address: /destinations/your-slug. Changing it breaks old links.'),
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('region')->required(),
                        Forms\Components\TextInput::make('price_from')->label('Starting price (৳ per person)')->required()->numeric()->minValue(0),
                        Forms\Components\TextInput::make('duration')->required()->helperText('e.g. 2–4 days'),
                        Forms\Components\TextInput::make('best_time')->required()->helperText('e.g. Nov – Mar'),
                        Forms\Components\TextInput::make('distance')->required()->helperText('e.g. ~400 km · 1 hr by air'),
                        Forms\Components\TextInput::make('style')->required()->helperText('e.g. Beach · Relaxed'),
                    ]),
                    Forms\Components\Textarea::make('tagline')->required()->rows(2)->helperText('Shown on the destination page header.'),
                    Forms\Components\Textarea::make('summary')->required()->rows(2)->helperText('Short blurb on the home page card.'),
                    Forms\Components\TextInput::make('overview_title')->required(),
                    Forms\Components\Grid::make(2)->schema([
                        self::photo('hero', 'Hero photo (wide, shown behind the title)'),
                        self::photo('card', 'Card photo (shown on the home and packages pages)'),
                    ]),
                    Forms\Components\Select::make('related')->label('Related destinations')->multiple()
                        ->options(fn (?Destination $record) => Destination::query()->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                            ->orderBy('sort_order')->get()->mapWithKeys(fn (Destination $d) => [$d->slug => $d->getTranslation('name', 'en')])->all())
                        ->helperText('Shown at the bottom of the page, in this order.'),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0)->helperText('Smaller numbers come first.'),
                        Forms\Components\Toggle::make('is_published')->default(true)->helperText('Turn off to hide it from the whole site.'),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make('Overview')->schema([
                    self::lines('overview', 'Paragraphs', long: true),
                    self::lines('highlights', 'Highlights'),
                    Forms\Components\Repeater::make('attractions')->collapsible()->itemLabel(fn (array $state) => $state['name'] ?? null)
                        ->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\Textarea::make('text')->required()->rows(2),
                            Forms\Components\TextInput::make('img')->label('Image URL')->url(),
                        ]),
                ]),
                Forms\Components\Tabs\Tab::make('Itinerary')->schema([
                    Forms\Components\Repeater::make('itinerary')->label('Days')->collapsible()->itemLabel(fn (array $state) => $state['title'] ?? null)
                        ->schema([
                            Forms\Components\TextInput::make('title')->required(),
                            self::lines('items', 'Plan for the day'),
                        ]),
                    Forms\Components\Grid::make(2)->schema([
                        self::lines('included', 'Included'),
                        self::lines('excluded', 'Not included'),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make('Practical')->schema([
                    Forms\Components\Repeater::make('seasons')->collapsible()->itemLabel(fn (array $state) => $state['range'] ?? null)
                        ->schema([
                            Forms\Components\Select::make('tone')->options(['best' => 'Best', 'good' => 'Good', 'wet' => 'Rainy'])->required(),
                            Forms\Components\TextInput::make('badge')->required(),
                            Forms\Components\TextInput::make('range')->required(),
                            Forms\Components\Textarea::make('text')->required()->rows(2),
                        ])->columns(2),
                    self::lines('transport', 'Getting there', long: true)->helperText('You can use <strong>By air:</strong> to bold the start.'),
                    self::lines('tips', 'Travel tips'),
                    Forms\Components\SpatieMediaLibraryFileUpload::make('gallery_photos')->label('Gallery photos')
                        ->collection('gallery')->multiple()->reorderable()->maxFiles(12)->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240)
                        ->helperText('Photos are matched to the captions below in order. Missing photos show a placeholder. JPG, PNG or WebP, up to 10 MB each.'),
                    self::lines('gallery', 'Photo captions')->helperText('One line per photo, in the same order as the photos above.'),
                ]),
                Forms\Components\Tabs\Tab::make('FAQ')->schema([
                    Forms\Components\Repeater::make('faq')->label('Questions')->collapsible()->itemLabel(fn (array $state) => $state['q'] ?? null)
                        ->schema([
                            Forms\Components\TextInput::make('q')->label('Question')->required(),
                            Forms\Components\Textarea::make('a')->label('Answer')->required()->rows(3),
                        ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('card')->collection('card')->conversion('card-400')->label('Photo')->width(64)->height(48),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('region'),
                Tables\Columns\TextColumn::make('price_from')->label('From')->money('BDT')->sortable(),
                Tables\Columns\TextColumn::make('reviews_count')->counts('reviews')->label('Reviews'),
                Tables\Columns\ToggleColumn::make('is_published')->label('Published'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\Action::make('view')->label('View on site')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Destination $record) => route('destination', $record->slug))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDestinations::route('/'),
            'create' => Pages\CreateDestination::route('/create'),
            'edit' => Pages\EditDestination::route('/{record}/edit'),
        ];
    }
}
