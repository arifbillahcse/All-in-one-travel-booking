<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PackageResource extends Resource
{
    use Translatable;

    /** Admin URLs use the record id, not the public slug (slugs can be edited). */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Plan')->columns(2)->schema([
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->alphaDash(),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('description')->required(),
                Forms\Components\TextInput::make('best_for')->required(),
                Forms\Components\TextInput::make('badge')->helperText('Optional ribbon, e.g. Most Popular.'),
                Forms\Components\TextInput::make('price')->label('Price (৳ per person)')->required()->numeric()->minValue(0),
                Forms\Components\TextInput::make('days')->required()->numeric()->minValue(1)->maxValue(60),
                Forms\Components\TextInput::make('nights')->required()->numeric()->minValue(0)->maxValue(60),
                Forms\Components\TextInput::make('destinations_count')->label('Number of destinations')->required()->numeric()->minValue(1)->default(1),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]),
            Forms\Components\Section::make('Card bullets')->schema([
                Forms\Components\Repeater::make('features')->simple(Forms\Components\TextInput::make('line')->required())->reorderable()->addActionLabel('Add bullet'),
            ]),
            Forms\Components\Section::make('Comparison table')->columns(2)->schema([
                Forms\Components\TextInput::make('hotel')->required(),
                Forms\Components\TextInput::make('meals')->required(),
                Forms\Components\TextInput::make('transport')->required(),
                Forms\Components\TextInput::make('cancellation')->required(),
                Forms\Components\Toggle::make('has_guide')->label('Local guide'),
                Forms\Components\Toggle::make('has_tickets')->label('Entry tickets and activities'),
                Forms\Components\Toggle::make('has_airport_transfer')->label('Airport pickup and drop'),
                Forms\Components\Toggle::make('has_trip_manager')->label('Dedicated trip manager'),
            ]),
            Forms\Components\Section::make('Visibility')->columns(2)->schema([
                Forms\Components\Toggle::make('is_featured')->label('Highlight as the featured plan'),
                Forms\Components\Toggle::make('is_published')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('BDT')->sortable(),
                Tables\Columns\TextColumn::make('days')->suffix(' days'),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\ToggleColumn::make('is_published')->label('Published'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
