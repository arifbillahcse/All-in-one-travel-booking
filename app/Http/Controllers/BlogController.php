<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private const PAGE_SIZE = 6;

    public function index(Request $request): View
    {
        $categories = PostCategory::orderBy('sort_order')
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();

        $category = $categories->firstWhere('slug', $request->query('category'));
        $search = trim((string) $request->query('q', ''));
        $show = max(self::PAGE_SIZE, min(120, (int) $request->query('show', self::PAGE_SIZE)));

        $posts = Post::published()->with(['category', 'media'])
            ->when($category, fn (Builder $q) => $q->where('post_category_id', $category->id))
            ->when($search !== '', function (Builder $q) use ($search) {
                $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
                $q->where(function (Builder $q) use ($like) {
                    foreach (['en', 'bn'] as $locale) {
                        $q->orWhere("title->{$locale}", 'like', $like)->orWhere("excerpt->{$locale}", 'like', $like);
                    }
                });
            })
            ->get();

        // The newest article is featured when nothing is filtered.
        $filtered = $category !== null || $search !== '';
        $featured = ! $filtered ? $posts->first() : null;
        $rest = $featured ? $posts->slice(1) : $posts;

        return view('pages.blog', [
            'noindex' => $search !== '',
            'categories' => $categories,
            'allCount' => $categories->sum('posts_count'),
            'category' => $category,
            'search' => $search,
            'show' => $show,
            'pageSize' => self::PAGE_SIZE,
            'total' => $posts->count(),
            'featured' => $featured,
            'posts' => $rest->take($show)->values(),
            'hasMore' => $rest->count() > $show,
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()->with(['category', 'destination', 'media'])->where('slug', $slug)->firstOrFail();

        $newestFirst = Post::published()->get(['id', 'slug', 'title', 'post_category_id']);
        $index = $newestFirst->search(fn (Post $p) => $p->id === $post->id);

        $related = $newestFirst->reject(fn (Post $p) => $p->id === $post->id)
            ->sortByDesc(fn (Post $p) => $p->post_category_id === $post->post_category_id)
            ->take(3)->pluck('id');

        $cover = $post->coverImage();

        return view('pages.blog-post', [
            'ogImage' => $cover->src,
            'ogType' => 'article',
            'publishedAt' => $post->published_at?->toIso8601String(),
            'post' => $post,
            'previous' => $newestFirst->get($index + 1),   // older
            'next' => $index > 0 ? $newestFirst->get($index - 1) : null,
            'related' => Post::published()->with(['category', 'media'])->whereIn('id', $related)->get()
                ->sortBy(fn (Post $p) => $related->search($p->id))->values(),
            'fullTitle' => $post->title.' | '.site('name'),
            'description' => $post->excerpt,
        ]);
    }
}
