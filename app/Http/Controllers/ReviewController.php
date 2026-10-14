<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewSubmissionRequest;
use App\Mail\ReviewSubmitted;
use App\Models\Destination;
use App\Models\Review;
use App\Services\WhatsAppMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            'noindex' => $request->query->has('destination') || $request->query->has('sort') || $request->query->has('show'),
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

    /**
     * A visitor's review. It is saved hidden (is_approved = false) until someone approves it
     * in the admin panel; the visitor is then sent to WhatsApp, as before.
     */
    public function store(ReviewSubmissionRequest $request, WhatsAppMessage $whatsapp): JsonResponse|RedirectResponse
    {
        if ($request->filled('website')) {
            return $this->done($request, whatsapp_url());
        }

        $data = $request->validated();
        $name = trim($data['name']);
        $text = trim($data['text']);

        $review = new Review([
            'destination_id' => Destination::published()->where('name->en', $data['destination'])->value('id'),
            'rating' => (int) $data['rating'],
            'reviewed_on' => now()->toDateString(),
            'is_approved' => false,
        ]);
        // Written in one language by the visitor; the team edits and translates when approving.
        $review->setTranslations('name', ['en' => $name, 'bn' => $name]);
        $review->setTranslations('city', ['en' => '', 'bn' => '']);
        $review->setTranslations('title', ['en' => '', 'bn' => '']);
        $review->setTranslations('body', ['en' => $text, 'bn' => $text]);
        $review->save();

        try {
            Mail::to(site('notify_email') ?: site('email'))->send(new ReviewSubmitted($review->load('destination')));
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->done($request, $whatsapp->url($whatsapp->review($data)));
    }

    private function done(Request $request, string $url): JsonResponse|RedirectResponse
    {
        return $request->expectsJson() ? response()->json(['whatsapp_url' => $url]) : redirect()->away($url);
    }
}
