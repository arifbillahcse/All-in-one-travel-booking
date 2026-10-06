<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InquiryStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $weekly = collect(range(6, 0))->map(fn (int $daysAgo) => Inquiry::whereDate('created_at', now()->subDays($daysAgo))->count())->all();

        return [
            Stat::make('New inquiries', Inquiry::new()->count())
                ->description('Waiting for a reply')->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color(Inquiry::new()->exists() ? 'danger' : 'success'),
            Stat::make('Last 7 days', Inquiry::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Bookings and messages')->chart($weekly)->color('primary'),
            Stat::make('Confirmed this month', Inquiry::where('status', 'confirmed')->where('updated_at', '>=', now()->startOfMonth())->count())
                ->description('Estimated ৳'.number_format((int) Inquiry::where('status', 'confirmed')->where('updated_at', '>=', now()->startOfMonth())->sum('estimated_total')))
                ->color('success'),
            Stat::make('Reviews to approve', Review::where('is_approved', false)->count())
                ->description('Submitted by travelers')->color(Review::where('is_approved', false)->exists() ? 'warning' : 'gray'),
        ];
    }
}
