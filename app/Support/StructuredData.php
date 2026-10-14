<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\Post;
use Illuminate\Support\HtmlString;

/** schema.org JSON-LD for search engines. Everything is built from the current page's data. */
final class StructuredData
{
    /** One <script type="application/ld+json"> tag. "<" and "&" are escaped so admin text can never close the tag. */
    public static function script(array $data): HtmlString
    {
        $json = json_encode(
            ['@context' => 'https://schema.org'] + $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR,
        );

        return new HtmlString('<script type="application/ld+json">'.$json.'</script>');
    }

    /** The site address with a trailing slash, the base of the @id values. */
    private static function root(): string
    {
        return rtrim(url('/'), '/').'/';
    }

    public static function organization(): array
    {
        $social = collect(['facebook', 'instagram', 'youtube'])
            ->map(fn (string $network) => site("social.{$network}"))
            ->filter(fn ($url) => is_string($url) && str_starts_with($url, 'http'))
            ->values()->all();

        return array_filter([
            '@type' => 'TravelAgency',
            '@id' => self::root().'#organization',
            'name' => site('name'),
            'url' => url('/'),
            'image' => url('images/og-default.png'),
            'email' => site('email'),
            'telephone' => site('phone'),
            'areaServed' => ['@type' => 'Country', 'name' => 'Bangladesh'],
            'sameAs' => $social ?: null,
        ]);
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::root().'#website',
            'name' => site('name'),
            'url' => url('/'),
            'inLanguage' => ['en', 'bn'],
            'publisher' => ['@id' => self::root().'#organization'],
        ];
    }

    /** @param list<array{0: string, 1: string}> $trail [name, url] pairs, home first */
    public static function breadcrumbs(array $trail): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($trail)->values()->map(fn (array $crumb, int $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb[0],
                'item' => $crumb[1],
            ])->all(),
        ];
    }

    public static function destination(Destination $destination, ?Image $hero): array
    {
        return array_filter([
            '@type' => 'TouristTrip',
            'name' => $destination->name,
            'description' => $destination->tagline,
            'url' => lroute('destination', $destination->slug),
            'image' => $hero?->src,
            'inLanguage' => app()->getLocale(),
            'touristType' => $destination->style,
            'provider' => ['@id' => self::root().'#organization'],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'BDT',
                'price' => (string) $destination->price_from,
                'url' => lroute('destination', $destination->slug).'#book',
                'availability' => 'https://schema.org/InStock',
            ],
        ]);
    }

    public static function faq(Destination $destination): array
    {
        return [
            '@type' => 'FAQPage',
            'mainEntity' => collect($destination->faq)->map(fn (array $item) => [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
            ])->values()->all(),
        ];
    }

    public static function post(Post $post, Image $cover): array
    {
        return array_filter([
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'image' => $cover->src,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'inLanguage' => app()->getLocale(),
            'mainEntityOfPage' => lroute('blog.post', $post->slug),
            'author' => ['@type' => 'Organization', 'name' => site('name')],
            'publisher' => ['@id' => self::root().'#organization'],
        ]);
    }
}
