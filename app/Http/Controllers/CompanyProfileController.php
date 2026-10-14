<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Destination;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Contracts\View\View;

/**
 * The print-ready company profile (A4). The same page is turned into a PDF with
 * `node tools/build-company-profile.mjs`. Only public marketing content is used here:
 * published destinations, packages, add-ons and approved reviews. No inquiries, no traveller contact details.
 */
class CompanyProfileController extends Controller
{
    public function show(): View
    {
        // The profile is an English business document; keep it English whatever the visitor's language.
        app()->setLocale('en');

        return view('company-profile', [
            'destinations' => Destination::published()->get(),
            'packages' => Package::published()->get(),
            'addons' => Addon::published()->get(),
            'stories' => Review::stories(3),
        ]);
    }
}
