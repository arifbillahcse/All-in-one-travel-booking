<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use Illuminate\Contracts\View\View;

class DestinationController extends Controller
{
    public function show(string $slug): View
    {
        $destination = Destination::published()->with('media')->where('slug', $slug)->firstOrFail();

        $hero = $destination->heroImage();

        return view('pages.destination', [
            'ogImage' => $hero?->src,
            'hero' => $hero,
            'destination' => $destination,
            'related' => $destination->relatedDestinations(),
            'packages' => Package::published()->get(),
            'fullTitle' => t('{name} Tour Packages | TravelOrio', ['name' => $destination->name]),
            'description' => t("Plan your {name} trip with TravelOrio: itinerary, what's included, best time to visit, travel tips and instant booking on WhatsApp. {tagline}", [
                'name' => $destination->name,
                'tagline' => $destination->tagline,
            ]),
        ]);
    }
}
