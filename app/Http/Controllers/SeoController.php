<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Post;
use App\Models\Review;
use App\Support\ContentCache;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/** robots.txt and sitemap.xml */
class SeoController extends Controller
{
    public function robots(): Response
    {
        if (config('travelorio.noindex')) {
            return response("User-agent: *\nDisallow: /\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            'Disallow: /inquiries',
            'Disallow: /bn/inquiries',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): Response
    {
        $xml = ContentCache::remember('sitemap', 3600, fn () => $this->buildSitemap());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function buildSitemap(): string
    {
        $pages = [
            ['home', [], 1.0, 'weekly', Destination::published()->max('updated_at')],
            ['packages', [], 0.9, 'weekly', Package::max('updated_at')],
            ['why-us', [], 0.6, 'monthly', null],
            ['reviews', [], 0.6, 'weekly', Review::approved()->max('updated_at')],
            ['blog', [], 0.8, 'weekly', Post::published()->max('updated_at')],
            ['contact', [], 0.5, 'yearly', null],
        ];

        foreach (Destination::published()->get(['slug', 'updated_at']) as $destination) {
            $pages[] = ['destination', ['slug' => $destination->slug], 0.9, 'monthly', $destination->updated_at];
        }

        foreach (Post::published()->get(['slug', 'updated_at']) as $post) {
            $pages[] = ['blog.post', ['slug' => $post->slug], 0.7, 'monthly', $post->updated_at];
        }

        $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($pages as [$route, $params, $priority, $frequency, $modified]) {
            $en = route($route, $params);
            $bn = route('bn.'.$route, $params);
            $lastmod = $modified ? Carbon::parse($modified)->toAtomString() : null;

            foreach ([$en, $bn] as $loc) {
                $out .= "  <url>\n    <loc>".e($loc)."</loc>\n";
                $out .= '    <xhtml:link rel="alternate" hreflang="en" href="'.e($en)."\"/>\n";
                $out .= '    <xhtml:link rel="alternate" hreflang="bn" href="'.e($bn)."\"/>\n";
                $out .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.e($en)."\"/>\n";
                $out .= $lastmod ? "    <lastmod>{$lastmod}</lastmod>\n" : '';
                $out .= "    <changefreq>{$frequency}</changefreq>\n    <priority>".number_format($priority, 1)."</priority>\n  </url>\n";
            }
        }

        return $out.'</urlset>'."\n";
    }
}
