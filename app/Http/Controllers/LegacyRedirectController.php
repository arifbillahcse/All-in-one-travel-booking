<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * The first version of the site was static HTML (packages.html, destination.html?place=sylhet ...).
 * Links shared or indexed back then keep working: they are redirected permanently (301)
 * to the new address, in the same language.
 */
class LegacyRedirectController extends Controller
{
    private const PAGES = [
        'index' => 'home',
        'packages' => 'packages',
        'reviews' => 'reviews',
        'contact' => 'contact',
        'why-us' => 'why-us',
        'blog' => 'blog',
    ];

    public function page(Request $request, string $page): RedirectResponse
    {
        return $this->go($request, self::PAGES[$page], [], except: ['lang']);
    }

    public function destination(Request $request): RedirectResponse
    {
        $slug = (string) $request->query('place', '');

        return $slug !== ''
            ? $this->go($request, 'destination', ['slug' => $slug], except: ['lang', 'place'])
            : $this->go($request, 'home', [], except: ['lang', 'place']);
    }

    public function post(Request $request): RedirectResponse
    {
        $slug = (string) $request->query('post', '');

        return $slug !== ''
            ? $this->go($request, 'blog.post', ['slug' => $slug], except: ['lang', 'post'])
            : $this->go($request, 'blog', [], except: ['lang', 'post']);
    }

    /** @param list<string> $except query parameters that are not carried over */
    private function go(Request $request, string $route, array $parameters, array $except): RedirectResponse
    {
        $prefix = $request->query('lang') === 'bn' ? 'bn.' : '';

        return redirect()->to(route($prefix.$route, $parameters + $request->except($except)), 301);
    }
}
