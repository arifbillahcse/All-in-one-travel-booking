<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private const PAGE_SIZE = 6;

    public function index(Request $request): View
    {
        $destinations = Destination::published()
            ->withCount(['reviews' => fn ($q) => $q->approved()])
            ->get()
            ->filter(fn (Destination $d) => $d->reviews_count > 0);

        $active = $destinations->firstWhere('slug', $request->query('destination'));
        $sort = $request->query('sort') === 'highest' ? 'highest' : 'newest';
        $show = max(self::PAGE_SIZE, min(120, (int) $request->query('show', self::PAGE_SIZE)));

        // Summary figures always cover every approved review, not only the filtered ones.
        $total = Review::approved()->count();
        $average = $total ? (float) Review::approved()->avg('rating') : 0;
        $breakdown = Review::approved()->selectRaw('rating, count(*) as total')->groupBy('rating')->pluck('total', 'rating');

        $list = Review::approved()->with('destination')
            ->when($active, fn ($q) => $q->where('destination_id', $active->id));
        $matching = (clone $list)->count();
        $reviews = ($sort === 'highest' ? $list->highestRated() : $list->newestFirst())->take($show)->get();

        $featured = Review::approved()->with('destination')->featured()->orderBy('sort_order')->first()
            ?? Review::approved()->with('destination')->orderBy('sort_order')->first();

        return view('pages.reviews', [
            'destinations' => $destinations,
            'active' => $active,
            'sort' => $sort,
            'show' => $show,
            'pageSize' => self::PAGE_SIZE,
            'total' => $total,
            'average' => $average,
            'breakdown' => $breakdown,
            'featured' => $featured,
            'reviews' => $reviews,
            'matching' => $matching,
        ]);
    }
}
