<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    use Translatable;

    /** Admin URLs use the record id, not the public slug (slugs can be edited). */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Blog posts';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Article')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                Forms\Components\Textarea::make('excerpt')->required()->rows(2)->columnSpanFull()->helperText('Shown on cards and in search results.'),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->alphaDash()
                    ->helperText('The article address: /blog/your-slug.'),
                Forms\Components\Select::make('post_category_id')->label('Category')->required()
                    ->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload(),
                Forms\Components\Select::make('destination_id')->label('Related destination')
                    ->relationship('destination', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload()
                    ->helperText('Adds a "Plan this trip" card at the end of the article.'),
                Forms\Components\TextInput::make('read_minutes')->numeric()->required()->minValue(1)->maxValue(120)->default(5),
            ]),
            Forms\Components\Section::make('Body')->schema([
                self::bodyBuilder(),
            ]),
            Forms\Components\Section::make('Publishing')->columns(2)->schema([
                Forms\Components\DateTimePicker::make('published_at')->default(now())->helperText('A future date schedules the article.'),
                Forms\Components\Toggle::make('is_published')->default(true)->helperText('Turn off to keep it as a draft.'),
                Forms\Components\SpatieMediaLibraryFileUpload::make('cover')->label('Cover photo')->collection('cover')->columnSpanFull()
                    ->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240)
                    ->helperText('JPG, PNG or WebP, up to 10 MB. It is resized automatically. Empty shows a placeholder.'),
            ]),
        ]);
    }

    /**
     * The article body is stored as an ordered list of blocks (p, h2, ul, tip), which the
     * public pages render. The builder keeps each block's fields under "data", so convert both ways.
     */
    private static function bodyBuilder(): Builder
    {
        return Builder::make('body')
            ->label('Content blocks')
            ->addActionLabel('Add block')
            ->collapsible()
            ->blocks([
                Block::make('p')->label('Paragraph')->icon('heroicon-o-bars-3-bottom-left')
                    ->schema([Forms\Components\Textarea::make('text')->required()->rows(4)]),
                Block::make('h2')->label('Heading')->icon('heroicon-o-hashtag')
                    ->schema([Forms\Components\TextInput::make('text')->required()]),
                Block::make('ul')->label('Bullet list')->icon('heroicon-o-list-bullet')
                    ->schema([Forms\Components\Repeater::make('items')->simple(Forms\Components\TextInput::make('item')->required())->addActionLabel('Add item')]),
                Block::make('tip')->label('Tip box')->icon('heroicon-o-light-bulb')
                    ->schema([Forms\Components\Textarea::make('text')->required()->rows(3)]),
            ])
            ->afterStateHydrated(fn (Builder $component, $state) => $component->state(self::toBuilderState($state)))
            ->mutateDehydratedStateUsing(fn ($state) => self::fromBuilderState($state));
    }

    /** @param array<int|string, array<string, mixed>>|null $blocks stored format -> builder format */
    public static function toBuilderState(?array $blocks): array
    {
        $state = [];

        foreach ($blocks ?? [] as $key => $block) {
            // The builder may add an empty "data" to a stored block, so look for the stored keys first.
            if (isset($block['data']) && ! array_key_exists('text', $block) && ! array_key_exists('items', $block)) {   // already in builder format
                $state[$key] = $block;

                continue;
            }

            $type = $block['type'] ?? 'p';
            $state[(string) Str::uuid()] = [
                'type' => $type,
                'data' => $type === 'ul' ? ['items' => self::toRepeaterItems($block['items'] ?? [])] : ['text' => $block['text'] ?? ''],
            ];
        }

        return $state;
    }

    /** The "simple" repeater inside the list block keeps each line as ['item' => text] under a uuid. */
    private static function toRepeaterItems(array $lines): array
    {
        $items = [];

        foreach ($lines as $line) {
            $items[(string) Str::uuid()] = ['item' => $line];
        }

        return $items;
    }

    /** @param array<string, array<string, mixed>>|null $state builder format -> stored format */
    public static function fromBuilderState(?array $state): array
    {
        return collect($state ?? [])->values()->map(fn (array $block) => ['type' => $block['type']] + ($block['type'] === 'ul'
            ? ['items' => array_values($block['data']['items'] ?? [])]
            : ['text' => $block['data']['text'] ?? '']))->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('cover')->collection('cover')->conversion('cover-600')->label('Cover')->width(64)->height(40),
                Tables\Columns\TextColumn::make('title')->searchable(query: fn ($query, string $search) => $query->where('title->en', 'like', "%{$search}%")->orWhere('title->bn', 'like', "%{$search}%"))->limit(50),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->badge(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
                Tables\Columns\ToggleColumn::make('is_published')->label('Published'),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('post_category_id')->label('Category')->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                Tables\Filters\TernaryFilter::make('is_published')->label('Status')->trueLabel('Published')->falseLabel('Draft'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('View on site')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Post $record) => route('blog.post', $record->slug))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
