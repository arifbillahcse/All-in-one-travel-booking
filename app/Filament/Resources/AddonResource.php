<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddonResource\Pages;
use App\Models\Addon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AddonResource extends Resource
{
    use Translatable;

    /** Admin URLs use the record id, not the public slug (slugs can be edited). */
    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $model = Addon::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Add-ons';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->columns(2)->schema([
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->alphaDash(),
            Forms\Components\Select::make('icon')->required()->options(['plane' => 'Plane', 'camera' => 'Camera', 'boat' => 'Boat', 'bed' => 'Bed']),
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\TextInput::make('description')->required(),
            Forms\Components\TextInput::make('price_from')->label('Price from (৳)')->required()->numeric()->minValue(0),
            Forms\Components\TextInput::make('price_unit')->helperText('Optional, e.g. / day or / room.'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('price_from')->money('BDT'),
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
            'index' => Pages\ListAddons::route('/'),
            'create' => Pages\CreateAddon::route('/create'),
            'edit' => Pages\EditAddon::route('/{record}/edit'),
        ];
    }
}
