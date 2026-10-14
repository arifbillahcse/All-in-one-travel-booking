@php
  $bdt = fn ($n) => 'BDT '.number_format($n);
  $site = site('name', config('travelorio.name'));
  $year = now()->year;
  $phone = site('phone_display');
  $email = site('email');
  $host = config('travelorio.website');
  $startingFrom = $destinations->min('price_from');
  $names = $destinations->pluck('name')->pipe(fn ($n) => $n->count() > 1 ? $n->slice(0, -1)->implode(', ').' and '.$n->last() : $n->implode(''));
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>{{ $site }} – Company Profile</title>
  <link rel="stylesheet" href="{{ asset('assets/css/company-profile.css') }}?v={{ @filemtime(public_path('assets/css/company-profile.css')) }}">
</head>
<body>

{{-- ============ 1. COVER ============ --}}
<section class="page page--cover">
  <div class="cover__top"><span></span><span></span><span></span><span></span></div>
  <div class="cover__card">
    <p class="cover__kicker">Company Profile</p>
    <div class="logo logo--xl">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5 5-2z"/></svg>
      <span>Travel<em>Orio</em></span>
    </div>
    <p class="cover__tagline">Curated journeys · Local guides · Direct booking</p>
    <hr class="cover__rule">
    <p class="cover__motto">{{ config('travelorio.tagline') }}</p>
    <p class="cover__place">Dhaka · Bangladesh</p>
  </div>
  <img class="cover__art" src="{{ asset('images/profile-cover.png') }}" alt="">
  <footer class="page__foot page__foot--cover"><span>{{ $site }}</span><span>01 | Company Profile {{ $year }}</span></footer>
</section>

{{-- ============ 2. EXECUTIVE PROFILE ============ --}}
<section class="page">
  <header class="page__head"><h1>Executive Profile</h1><span>01 | Corporate identity</span></header>
  <div class="page__body">
    <h2 class="band">Who we are</h2>
    <p class="lead">{{ $site }} is a Dhaka-based travel company that designs and runs small-group and private journeys to Bangladesh's most loved destinations: {{ $names }}. Every trip is hand-planned, led by local guides and sold at one clear, itemised price. Customers talk to a real person on WhatsApp from the first question to the ride home.</p>

    <ul class="stats">
      <li><strong>{{ $destinations->count() }}</strong><span>Destinations</span></li>
      <li><strong>{{ $packages->count() }}</strong><span>Ready-made plans</span></li>
      <li><strong>5,000+</strong><span>Travelers served</span></li>
      <li><strong>4.9 / 5</strong><span>Average rating</span></li>
    </ul>

    <div class="cols">
      <div class="box box--sand">
        <h3>Vision</h3>
        <p>To be the most trusted way to see Bangladesh, for visitors from Dhaka and from around the world, by making every journey safe, transparent and personal.</p>
        <h3>Mission</h3>
        <p>To plan well-organised trips with local expertise, honest pricing and responsive support, and to keep the benefits of tourism in the communities we visit by working with local guides, hotels and boat operators.</p>
      </div>
      <div class="box box--foam">
        <h3>Corporate details</h3>
        <dl class="facts">
          <dt>Trading name</dt><dd>{{ $site }}</dd>
          <dt>Head office</dt><dd>House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh</dd>
          <dt>Business</dt><dd>Domestic and inbound tour operator</dd>
          <dt>Languages</dt><dd>Bangla and English</dd>
          <dt>Support hours</dt><dd>Sat – Thu, 9 AM – 9 PM (WhatsApp until 6 PM on Fridays)</dd>
          @if (site('company_registration'))<dt>Registration</dt><dd>{{ site('company_registration') }}</dd>@endif
          @if (site('trade_license'))<dt>Trade licence</dt><dd>{{ site('trade_license') }}</dd>@endif
          @if (site('tin'))<dt>TIN</dt><dd>{{ site('tin') }}</dd>@endif
        </dl>
      </div>
    </div>

    <h2 class="band">How we work</h2>
    <ol class="steps">
      <li><b>1</b><h4>Request</h4><p>The customer picks a place or plan and sends a request through the website or WhatsApp.</p></li>
      <li><b>2</b><h4>Quote</h4><p>A planner replies with an itemised quote. Nothing is paid at this stage.</p></li>
      <li><b>3</b><h4>Confirm</h4><p>Once the plan is agreed, a deposit by bKash, Nagad or bank transfer locks the dates.</p></li>
      <li><b>4</b><h4>Travel</h4><p>A local guide hosts the trip, with a 24/7 emergency contact and live updates on WhatsApp.</p></li>
    </ol>

    <h2 class="band">Included in every trip</h2>
    <ul class="checks">
      <li>Hand-planned itinerary</li><li>Local guide</li><li>Vetted hotels</li><li>Private AC transport</li>
      <li>Entry fees and permits handled</li><li>Itemised, transparent quote</li><li>24/7 WhatsApp support</li><li>Free cancellation up to 7 days before</li>
    </ul>
  </div>
  <footer class="page__foot"><span>{{ $site }}</span><span>02 | Company Profile</span></footer>
</section>

{{-- ============ 3. DESTINATIONS ============ --}}
<section class="page">
  <header class="page__head"><h1>Our Destinations</h1><span>02 | Product range</span></header>
  <div class="page__body">
    <h2 class="band">Where we take our guests</h2>
    <p class="lead">Distinct landscapes, from the world's longest natural sea beach to the largest mangrove forest on Earth. Each has its own itinerary, best season and starting price.</p>
    <div class="dests">
      @foreach ($destinations as $d)
      <article class="dest">
        <div class="dest__icon">@include('partials.icons.destination', ['slug' => $d->slug])</div>
        <div class="dest__main">
          <h3>{{ $d->name }}</h3>
          <p class="dest__meta">{{ $d->region }} · {{ $d->duration }}</p>
          <p>{{ $d->summary }}</p>
          <ul class="dest__list">
            @foreach (array_slice((array) $d->highlights, 0, 3) as $h)<li>{{ $h }}</li>@endforeach
          </ul>
          <p class="dest__foot"><span>Best time: <b>{{ $d->best_time }}</b></span><span class="price">From {{ $bdt($d->price_from) }}</span></p>
        </div>
      </article>
      @endforeach
    </div>
  </div>
  <footer class="page__foot"><span>{{ $site }}</span><span>03 | Company Profile</span></footer>
</section>

{{-- ============ 4. PACKAGES ============ --}}
<section class="page">
  <header class="page__head"><h1>Packages &amp; Pricing</h1><span>03 | Commercial offer</span></header>
  <div class="page__body">
    <h2 class="band">Three plans, one transparent price</h2>
    <p class="lead">Prices are per person on a twin-sharing basis. The final quote depends on destination, season and hotel category. Groups of six or more receive a reduced per-person price and a dedicated trip manager.</p>

    <div class="plans">
      @foreach ($packages as $p)
      <article class="plan @if($p->is_featured) plan--hot @endif">
        @if ($p->is_featured)<span class="plan__badge">Most popular</span>@endif
        <h3>{{ $p->name }}</h3>
        <p class="plan__desc">{{ $p->best_for }}</p>
        <p class="plan__price">{{ $bdt($p->price) }}<small> / person</small></p>
        <ul>
          @foreach ((array) $p->features as $f)<li>{{ $f }}</li>@endforeach
        </ul>
      </article>
      @endforeach
    </div>

    <h2 class="band">What is in each plan</h2>
    <table class="compare">
      <thead><tr><th>Feature</th>@foreach ($packages as $p)<th>{{ $p->name }}</th>@endforeach</tr></thead>
      <tbody>
        <tr><td>Price per person</td>@foreach ($packages as $p)<td>{{ $bdt($p->price) }}</td>@endforeach</tr>
        <tr><td>Duration</td>@foreach ($packages as $p)<td>{{ $p->days }} {{ \Illuminate\Support\Str::plural('day', $p->days) }}, {{ $p->nights }} {{ \Illuminate\Support\Str::plural('night', $p->nights) }}</td>@endforeach</tr>
        <tr><td>Destinations</td>@foreach ($packages as $p)<td>{{ $p->destinations_count }}</td>@endforeach</tr>
        <tr><td>Hotel</td>@foreach ($packages as $p)<td>{{ $p->hotel }}</td>@endforeach</tr>
        <tr><td>Meals</td>@foreach ($packages as $p)<td>{{ $p->meals }}</td>@endforeach</tr>
        <tr><td>Transport</td>@foreach ($packages as $p)<td>{{ $p->transport }}</td>@endforeach</tr>
        <tr><td>Local guide</td>@foreach ($packages as $p)<td>{{ $p->has_guide ? 'Included' : '–' }}</td>@endforeach</tr>
        <tr><td>Entry tickets &amp; activities</td>@foreach ($packages as $p)<td>{{ $p->has_tickets ? 'Included' : '–' }}</td>@endforeach</tr>
        <tr><td>Airport pickup &amp; drop</td>@foreach ($packages as $p)<td>{{ $p->has_airport_transfer ? 'Included' : '–' }}</td>@endforeach</tr>
        <tr><td>Dedicated trip manager</td>@foreach ($packages as $p)<td>{{ $p->has_trip_manager ? 'Included' : '–' }}</td>@endforeach</tr>
        <tr><td>Free cancellation</td>@foreach ($packages as $p)<td>{{ $p->cancellation }}</td>@endforeach</tr>
      </tbody>
    </table>

    <h2 class="band">Optional extras</h2>
    <ul class="extras">
      @foreach ($addons as $a)
      <li><b>{{ $a->title }}</b><span>from {{ $bdt($a->price_from) }}{{ $a->price_unit ? ' '.$a->price_unit : '' }}</span></li>
      @endforeach
    </ul>
  </div>
  <footer class="page__foot"><span>{{ $site }}</span><span>04 | Company Profile</span></footer>
</section>

{{-- ============ 5. WHY US + CONTACT ============ --}}
<section class="page">
  <header class="page__head"><h1>Why {{ $site }}</h1><span>04 | Assurance &amp; contact</span></header>
  <div class="page__body">
    <h2 class="band">Four promises we never compromise on</h2>
    <div class="promises">
      <div><h4>Local expert guides</h4><p>Born and raised in the regions they show. Bangla and English speakers, trained in first aid and local safety, with small groups only.</p></div>
      <div><h4>Honest pricing</h4><p>One itemised price per person, in writing, before anything is paid. Free cancellation up to 7 days before departure.</p></div>
      <div><h4>Safe and insured</h4><p>Vetted hotels, licensed boats, permits arranged for the guest, life jackets and first-aid kits on every trip, and a 24/7 emergency contact.</p></div>
      <div><h4>WhatsApp support</h4><p>Real people, not a bot. Typical reply within an hour in opening hours, with live updates on weather, ship and road conditions.</p></div>
    </div>

    @if ($stories->isNotEmpty())
    <h2 class="band">What travelers say</h2>
    <div class="quotes">
      @foreach ($stories as $s)
      <blockquote>
        <p class="stars">★★★★★</p>
        <p>“{{ $s->body }}”</p>
        <cite>{{ \Illuminate\Support\Str::of($s->name)->explode(' ')->first() }} · {{ $s->city }}@if($s->destination) · {{ $s->destination->name }}@endif</cite>
      </blockquote>
      @endforeach
    </div>
    @endif

    <h2 class="band">Good to know</h2>
    <dl class="faq">
      <dt>Are your guides licensed?</dt><dd>Yes. Guides hold the local permits each area requires, and Sundarbans trips are accompanied by forest department rangers as the law requires.</dd>
      <dt>What if bad weather cancels the trip?</dt><dd>Safety comes first. If a boat, ship or road is not safe, the trip is rescheduled or everything not already paid to hotels and operators is refunded.</dd>
      <dt>How does payment work?</dt><dd>No payment is taken online. After a request we confirm availability and share bKash, Nagad or bank-transfer details; a deposit is asked only once the plan is confirmed.</dd>
      <dt>Do you handle groups and companies?</dt><dd>Yes: corporate retreats, family reunions and school tours. Groups of six or more get a reduced per-person price and a dedicated trip manager.</dd>
    </dl>

    <div class="contact">
      <h2>Head office &amp; contact</h2>
      <div class="contact__grid">
        <div>
          <p><b>{{ $site }}</b><br>House 12, Road 5<br>Dhanmondi, Dhaka 1205<br>Bangladesh</p>
        </div>
        <div>
          <p><span>WhatsApp / Phone</span><br>{{ $phone }}</p>
          <p><span>Email</span><br>{{ $email }}</p>
        </div>
        <div>
          <p><span>Website</span><br>{{ $host }}</p>
          <p><span>Hours</span><br>Sat – Thu · 9 AM – 9 PM</p>
        </div>
      </div>
    </div>
  </div>
  <footer class="page__foot"><span>{{ $site }}</span><span>05 | Company Profile</span></footer>
</section>

</body>
</html>
