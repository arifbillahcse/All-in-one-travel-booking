<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Post;
use App\Models\Review;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'destinations' => Destination::published()->with('media')->get(),
            'packages' => Package::published()->get(),
            'stories' => Review::stories(3),
            'posts' => Post::published()->with(['category', 'media'])->take(3)->get(),
        ]);
    }

    public function whyUs(): View
    {
        return view('pages.why-us', ['stories' => Review::stories(3)]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }
}
