<?php

namespace App\Filament\Widgets;

use App\Models\Destination;
use App\Models\Inquiry;
use Filament\Widgets\ChartWidget;

class InquiriesByDestination extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Booking requests by destination';

    protected function getData(): array
    {
        $counts = Inquiry::where('type', 'booking')->selectRaw('destination_id, count(*) as total')->groupBy('destination_id')->pluck('total', 'destination_id');
        $destinations = Destination::orderBy('sort_order')->get();

        return [
            'datasets' => [[
                'label' => 'Requests',
                'data' => $destinations->map(fn (Destination $d) => (int) ($counts[$d->id] ?? 0))->all(),
                'backgroundColor' => '#0a6ea8',
            ]],
            'labels' => $destinations->map(fn (Destination $d) => $d->getTranslation('name', 'en'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
