<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Phase 2: pages render their Blade views. Content is still supplied by the
 * legacy scripts in public/assets/js; Phases 3-5 move it to the database.
 */
class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function packages(): View
    {
        return view('pages.packages');
    }

    public function reviews(): View
    {
        return view('pages.reviews');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function whyUs(): View
    {
        return view('pages.why-us');
    }

    public function blog(): View
    {
        return view('pages.blog');
    }

    public function post(string $slug): View
    {
        abort_unless(in_array($slug, site('blog_slugs'), true), 404);

        return view('pages.blog-post', ['slug' => $slug]);
    }

    public function destination(string $slug): View
    {
        $name = site("destinations.$slug");
        abort_if($name === null, 404);

        return view('pages.destination', [
            'slug' => $slug,
            'title' => $name.' Tour Packages',
        ]);
    }
}
