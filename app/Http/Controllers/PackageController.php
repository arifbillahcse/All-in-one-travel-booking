<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Destination;
use App\Models\Package;
use Illuminate\Contracts\View\View;

class PackageController extends Controller
{
    public function index(): View
    {
        return view('pages.packages', [
            'packages' => Package::published()->get(),
            'addons' => Addon::published()->get(),
            'destinations' => Destination::published()->with('media')->get(),
        ]);
    }
}
