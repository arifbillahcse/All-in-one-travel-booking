<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\InquiryResource;
use App\Models\Inquiry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestInquiries extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest inquiries';

    public function table(Table $table): Table
    {
        return $table
            ->query(Inquiry::query()->with(['destination'])->latest()->limit(8))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Received')->since(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('destination.name')->label('Destination')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'new' => 'danger', 'contacted' => 'warning', 'confirmed' => 'success', default => 'gray',
                }),
            ])
            ->recordUrl(fn (Inquiry $record) => InquiryResource::getUrl('edit', ['record' => $record]));
    }
}
