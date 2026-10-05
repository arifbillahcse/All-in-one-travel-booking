<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Phase 1: every public page renders the shared layout with a placeholder body.
 * Phase 2 replaces each method with the real Blade page.
 */
class PageController extends Controller
{
    public function home(): View
    {
        return $this->page('Home');
    }

    public function packages(): View
    {
        return $this->page('Packages');
    }

    public function reviews(): View
    {
        return $this->page('Reviews');
    }

    public function contact(): View
    {
        return $this->page('Contact');
    }

    public function whyUs(): View
    {
        return $this->page('Why Us');
    }

    public function blog(): View
    {
        return $this->page('Blog');
    }

    public function post(string $slug): View
    {
        return $this->page('Blog article');
    }

    public function destination(string $slug): View
    {
        $name = site("destinations.$slug");
        abort_if($name === null, 404);

        return $this->page($name);
    }

    private function page(string $title): View
    {
        return view('pages.placeholder', ['title' => $title]);
    }
}
