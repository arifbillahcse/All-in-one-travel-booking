<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Actions;
use App\Models\Inquiry;
use Filament\Resources\Pages\ListRecords;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')->label('Export all (CSV)')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->action(fn () => InquiryResource::csv(Inquiry::with(['destination', 'package'])->latest()->cursor())),
        ];
    }
}
