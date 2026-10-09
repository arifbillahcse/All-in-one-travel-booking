<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ReviewResource extends Resource
{
    use Translatable;

    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    /** Reviews waiting for a decision. */
    public static function getNavigationBadge(): ?string
    {
        $pending = Review::where('is_approved', false)->count();

        return $pending ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->columns(2)->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('city')->helperText('Shown as "City · Destination".'),
            Forms\Components\Select::make('destination_id')->label('Destination')
                ->relationship('destination', 'slug')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                ->searchable(false)->preload(),
            Forms\Components\Select::make('traveler_type')->options(['Family' => 'Family', 'Couple' => 'Couple', 'Friends' => 'Friends', 'Solo' => 'Solo'])->placeholder('—'),
            Forms\Components\Select::make('rating')->required()->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star']),
            Forms\Components\DatePicker::make('reviewed_on')->required()->default(now()),
            Forms\Components\TextInput::make('title')->columnSpanFull(),
            Forms\Components\Textarea::make('body')->required()->rows(5)->columnSpanFull(),
            Forms\Components\Toggle::make('is_approved')->label('Approved (visible on the site)')->default(true),
            Forms\Components\Toggle::make('is_featured')->label('Featured quote on the reviews page'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(query: fn ($query, string $search) => $query->where('name->en', 'like', "%{$search}%")->orWhere('name->bn', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('destination.name')->label('Destination'),
                Tables\Columns\TextColumn::make('rating')->formatStateUsing(fn (int $state) => stars($state)),
                Tables\Columns\TextColumn::make('body')->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('reviewed_on')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('Approved'),
            ])
            ->defaultSort('reviewed_on', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('Approval')->trueLabel('Approved')->falseLabel('Waiting for approval'),
                Tables\Filters\SelectFilter::make('destination_id')->label('Destination')->relationship('destination', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Review $record) => ! $record->is_approved)
                    ->action(fn (Review $record) => $record->update(['is_approved' => true])),
                Tables\Actions\Action::make('hide')->icon('heroicon-o-eye-slash')->color('gray')
                    ->visible(fn (Review $record) => $record->is_approved)
                    ->requiresConfirmation()
                    ->action(fn (Review $record) => $record->update(['is_approved' => false])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
