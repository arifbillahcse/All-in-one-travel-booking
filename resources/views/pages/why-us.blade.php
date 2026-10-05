@extends('layouts.app', [
  'title' => 'Why Choose Us',
  'description' => 'Local expert guides, honest per-person pricing, vetted hotels and real WhatsApp support: see why travelers choose TravelOrio for Bangladesh.',
  'bodyClass' => 'page-whyus',
])

@section('content')
    <!-- =====================================================
         1. PAGE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="{{ __('Why choose us introduction') }}">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>

      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
          <ol>
            <li><a href="{{ lroute('home') }}">{{ __('Home') }}</a></li>
            <li aria-current="page">{{ __('Why Us') }}</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow">{{ __('Why TravelOrio') }}</p>
        <h1 class="page-hero__title">{{ __('Travel with people') }} <em>{{ __('who know the way.') }}</em></h1>
        <p class="page-hero__lead">{{ __('Local guides, honest prices and real people on WhatsApp. Here is what makes a TravelOrio trip different.') }}</p>
        <div class="page-hero__actions">
          <a href="{{ lroute('packages') }}" class="btn btn--primary btn--lg">{{ __('Plan My Trip') }}</a>
          <a href="{{ lroute('reviews') }}" class="btn btn--ghost btn--lg">{{ __('Read Reviews') }}</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. NUMBERS
         ===================================================== -->
    <section class="section section--tight" id="numbers">
      <div class="container">
        <div class="stats stats--lifted" data-reveal>
          <div class="stat"><span class="stat__num" data-count="5000">5,000</span><span class="stat__label">{{ __('Happy travelers') }}</span></div>
          <div class="stat"><span class="stat__num" data-count="6">6</span><span class="stat__label">{{ __('Destinations') }}</span></div>
          <div class="stat"><span class="stat__num" data-count="8">8</span><span class="stat__label">{{ __('Years of experience') }}</span></div>
          <div class="stat"><span class="stat__num" data-count="4.9" data-decimals="1">4.9</span><span class="stat__label">{{ __('Average rating') }}</span></div>
        </div>
      </div>
    </section>


    <!-- =====================================================
         3. FOUR PILLARS
         ===================================================== -->
    <section class="section" id="pillars">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Our promise') }}</p>
          <h2 class="section__title">{{ __('Four things we never compromise on') }}</h2>
          <p class="section__lead">{{ __('Every trip is built on these, from the first message to the ride home.') }}</p>
        </header>

        <div class="grid grid--2 pillars">
          <article class="pillar" data-reveal>
            <span class="pillar__num" aria-hidden="true">01</span>
            <div class="pillar__icon" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg></div>
            <h3 class="pillar__title">{{ __('Local Expert Guides') }}</h3>
            <p class="pillar__text">{{ __('Our guides are born and raised in the regions you visit. They speak Bangla and English, know the quiet corners and the best light for photos, and stay with your group the whole way.') }}</p>
            <ul class="tick-list">
              <li>{{ __('Bangla and English speakers') }}</li>
              <li>{{ __('Small groups, never a crowd') }}</li>
              <li>{{ __('Trained in first aid and local safety') }}</li>
            </ul>
          </article>
          <article class="pillar" data-reveal>
            <span class="pillar__num" aria-hidden="true">02</span>
            <div class="pillar__icon" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M14.8 9.2c-.6-.8-1.6-1.2-2.8-1.2-1.7 0-2.8.9-2.8 2 0 3 5.6 1.4 5.6 4.2 0 1.2-1.2 2-2.8 2-1.3 0-2.3-.5-3-1.4M12 6v2m0 8v2"/></svg></div>
            <h3 class="pillar__title">{{ __('Honest Pricing') }}</h3>
            <p class="pillar__text">{{ __('You get one clear price per person, with the quote itemised before you pay anything. No surprise charges at the hotel, on the boat or at the end of the trip.') }}</p>
            <ul class="tick-list">
              <li>{{ __('Itemised quote in writing') }}</li>
              <li>{{ __('Pay by bKash, Nagad or bank transfer') }}</li>
              <li>{{ __('Free cancellation up to 7 days before departure') }}</li>
            </ul>
          </article>
          <article class="pillar" data-reveal>
            <span class="pillar__num" aria-hidden="true">03</span>
            <div class="pillar__icon" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/></svg></div>
            <h3 class="pillar__title">{{ __('Safe and Insured') }}</h3>
            <p class="pillar__text">{{ __('We work only with hotels we have visited and boats that carry a licence. Permits are arranged for you, and someone on our team follows your trip from start to finish.') }}</p>
            <ul class="tick-list">
              <li>{{ __('Vetted hotels and licensed boats') }}</li>
              <li>{{ __('Life jackets and first-aid kits on every trip') }}</li>
              <li>{{ __('A 24/7 emergency contact') }}</li>
            </ul>
          </article>
          <article class="pillar" data-reveal>
            <span class="pillar__num" aria-hidden="true">04</span>
            <div class="pillar__icon" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L3 21l2-5.2A8 8 0 1 1 21 12z"/><path d="M9 10h6M9 14h4"/></svg></div>
            <h3 class="pillar__title">{{ __('WhatsApp Support') }}</h3>
            <p class="pillar__text">{{ __('Message us before you book, while you travel, or after you get home. You always talk to a real person from our team, never a bot.') }}</p>
            <ul class="tick-list">
              <li>{{ __('Replies in about an hour during opening hours') }}</li>
              <li>{{ __('Live updates on weather, ship and road conditions') }}</li>
              <li>{{ __('Help with changes at any time') }}</li>
            </ul>
          </article>
        </div>
      </div>
    </section>


    <!-- =====================================================
         4. INCLUDED IN EVERY TRIP
         ===================================================== -->
    <section class="section section--alt" id="included">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Included in every trip') }}</p>
          <h2 class="section__title">{{ __('The basics, done properly') }}</h2>
        </header>

        <ul class="include-grid">
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('A hand-planned itinerary') }}</span></li>
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('A local guide') }}</span></li>
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('Vetted hotels') }}</span></li>
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('Private AC transport') }}</span></li>
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('Entry fees and permits handled') }}</span></li>
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('Itemised, transparent quote') }}</span></li>
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('24/7 WhatsApp support') }}</span></li>
          <li class="include-item" data-reveal><span class="include-item__tick" aria-hidden="true"></span><span>{{ __('Free cancellation up to 7 days before') }}</span></li>
        </ul>
      </div>
    </section>


    <!-- =====================================================
         5. TRAVELER STORIES (rendered from js/data.js)
         ===================================================== -->
    <section class="section" id="stories">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Real stories') }}</p>
          <h2 class="section__title">{{ __('Travelers trust us') }}</h2>
        </header>

        <div class="grid grid--3" id="why-reviews"></div>

        <p class="section__more" data-reveal>
          <a href="{{ lroute('reviews') }}" class="link-arrow">{{ __('Read all traveler reviews') }} <span aria-hidden="true">→</span></a>
        </p>
      </div>
    </section>


    <!-- =====================================================
         6. FAQ
         ===================================================== -->
    <section class="section section--alt" id="faq">
      <div class="container container--narrow">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Good to know') }}</p>
          <h2 class="section__title">{{ __('Questions about trusting us') }}</h2>
        </header>

        <div class="faq" data-reveal>
          <details class="faq__item">
            <summary>{{ __('Are your guides licensed?') }}</summary>
            <p>{{ __('Yes. Our guides hold the local permits required for each area, and Sundarbans trips are accompanied by forest department rangers as the law requires.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('What happens if bad weather cancels the trip?') }}</summary>
            <p>{{ __('Safety comes first. If a boat, ship or road is not safe, we reschedule your trip or refund what we have not already paid to hotels and operators.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Can I speak to someone before I pay?') }}</summary>
            <p>{{ __('Of course. Message or call us, ask anything, and take your time. Payment is only requested once you have confirmed your plan.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Do you handle group and family trips?') }}</summary>
            <p>{{ __('Yes. Groups of six or more get a lower per-person price and a dedicated trip manager. We also plan family trips with children and older travelers in mind.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Who is behind TravelOrio?') }}</summary>
            <p>{{ __('We are a small team based in Dhaka who love Bangladesh. Every itinerary is checked by someone who has been there.') }}</p>
          </details>
        </div>
      </div>
    </section>


    <!-- =====================================================
         7. CLOSING CTA
         ===================================================== -->
    <section class="cta-band" aria-label="{{ __('Plan your trip') }}">
      <div class="container cta-band__inner" data-reveal>
        <div>
          <h2 class="cta-band__title">{!! __('Ready to travel with <em>people who know the way?</em>') !!}</h2>
          <p>{{ __('Pick a plan and we will take care of the rest.') }}</p>
        </div>
        <a href="{{ lroute('packages') }}" class="btn btn--primary btn--lg">{{ __('View Packages') }}</a>
      </div>
    </section>

  
@endsection

@push('scripts-data')
  <script src="{{ asset_js('data.js') }}" defer></script>
@endpush

@push('scripts-i18n')
  <script src="{{ asset_js('i18n/data-bn.js') }}" defer></script>
@endpush

@push('scripts')
  <script src="{{ asset_js('whyus.js') }}" defer></script>
@endpush
