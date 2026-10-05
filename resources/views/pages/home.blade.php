@extends('layouts.app', [
  'title' => null,
  'description' => 'Book curated tours to Cox\'s Bazar, Sundarbans, Sylhet, Bandarban, Saint Martin and Kuakata with local guides and transparent pricing.',
  'bodyClass' => '',
])

@section('content')
    <!-- =====================================================
         2. HERO (layered paper-cut landscape)
         ===================================================== -->
    <section class="hero" id="home" aria-label="{{ __('Welcome') }}">

      <!-- Animated paper-cut scene. Each layer has data-depth for parallax. -->
      <div class="hero__scene" aria-hidden="true">

        <div class="hero__layer hero__sky" data-depth="0.02"></div>

        <!-- Sun on the horizon -->
        <div class="hero__layer hero__sun-wrap" data-depth="0.04"><div class="hero__sun"></div></div>

        <!-- Clouds (soft, drift on the right side only) -->
        <div class="hero__layer hero__clouds" data-depth="0.06">
          <svg class="cloud cloud--1" viewBox="0 0 220 80" xmlns="http://www.w3.org/2000/svg"><path d="M30 70a24 24 0 0 1 4-47 36 36 0 0 1 68-8 30 30 0 0 1 50 14 26 26 0 0 1 36 41z"/></svg>
          <svg class="cloud cloud--2" viewBox="0 0 220 80" xmlns="http://www.w3.org/2000/svg"><path d="M30 70a24 24 0 0 1 4-47 36 36 0 0 1 68-8 30 30 0 0 1 50 14 26 26 0 0 1 36 41z"/></svg>
          <svg class="cloud cloud--3" viewBox="0 0 220 80" xmlns="http://www.w3.org/2000/svg"><path d="M30 70a24 24 0 0 1 4-47 36 36 0 0 1 68-8 30 30 0 0 1 50 14 26 26 0 0 1 36 41z"/></svg>
        </div>

        <!-- Birds -->
        <div class="hero__layer hero__birds" data-depth="0.08">
          <svg class="bird bird--1" viewBox="0 0 40 16" xmlns="http://www.w3.org/2000/svg"><path d="M2 12C10 0 16 2 20 10 24 2 30 0 38 12 30 6 24 6 20 14 16 6 10 6 2 12z"/></svg>
          <svg class="bird bird--2" viewBox="0 0 40 16" xmlns="http://www.w3.org/2000/svg"><path d="M2 12C10 0 16 2 20 10 24 2 30 0 38 12 30 6 24 6 20 14 16 6 10 6 2 12z"/></svg>
          <svg class="bird bird--3" viewBox="0 0 40 16" xmlns="http://www.w3.org/2000/svg"><path d="M2 12C10 0 16 2 20 10 24 2 30 0 38 12 30 6 24 6 20 14 16 6 10 6 2 12z"/></svg>
        </div>

        <!-- Distant islands on the horizon -->
        <div class="hero__layer hero__islands" data-depth="0.10">
          <svg viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 200V158C70 146 130 100 205 118 245 74 300 72 332 118 384 138 446 172 540 200z"/>
            <path d="M820 200C900 178 980 138 1056 148 1104 112 1170 112 1214 150 1290 156 1356 168 1440 160V200z"/>
            <g class="palms">
              <path d="M1128 150c0-22 3-38 10-54" fill="none"/>
              <path d="M1138 96c-10-12-26-11-35 0 12-4 23-2 35 0zM1138 96c10-12 27-11 36 0-12-4-24-2-36 0zM1138 96c-5-16 3-28 14-32-6 9-8 20-14 32z" stroke="none"/>
              <path d="M1176 154c0-16 2-28 7-40" fill="none"/>
              <path d="M1183 114c-8-9-20-8-27 0 9-3 18-2 27 0zM1183 114c8-9 21-8 28 0-10-3-19-2-28 0z" stroke="none"/>
            </g>
          </svg>
        </div>

        <!-- Sea, sun reflection, first wave, ship, second wave -->
        <div class="hero__layer hero__sea" data-depth="0.14"></div>
        <div class="hero__layer hero__glint" data-depth="0.14"></div>
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>

        <div class="hero__layer hero__ship" data-depth="0.22">
            <svg class="ship" viewBox="0 0 300 150" xmlns="http://www.w3.org/2000/svg">
              <circle class="ship__smoke" cx="193" cy="14" r="9"/>
              <circle class="ship__smoke ship__smoke--2" cx="199" cy="11" r="7"/>
              <rect class="ship__funnel" x="182" y="22" width="22" height="26" rx="3"/>
              <rect class="ship__hull" x="182" y="31" width="22" height="5"/>
              <rect x="148" y="4" width="2.5" height="28" fill="#cfe6ef"/>
              <path class="ship__flag" d="M150.500 4L172 9.500L150.500 15Z"/>
              <rect class="ship__cabin" x="126" y="30" width="48" height="19" rx="3"/>
              <g class="ship__win"><rect x="132" y="35" width="9" height="7" rx="1.500"/><rect x="146" y="35" width="9" height="7" rx="1.500"/><rect x="160" y="35" width="9" height="7" rx="1.500"/></g>
              <rect class="ship__cabin" x="82" y="48" width="136" height="25" rx="4"/>
              <g class="ship__win"><rect x="90" y="54" width="10" height="8" rx="1.500"/><rect x="106" y="54" width="10" height="8" rx="1.500"/><rect x="122" y="54" width="10" height="8" rx="1.500"/><rect x="138" y="54" width="10" height="8" rx="1.500"/><rect x="154" y="54" width="10" height="8" rx="1.500"/><rect x="170" y="54" width="10" height="8" rx="1.500"/><rect x="186" y="54" width="10" height="8" rx="1.500"/><rect x="202" y="54" width="10" height="8" rx="1.500"/></g>
              <rect class="ship__deck" x="44" y="72" width="212" height="30" rx="4"/>
              <g class="ship__win"><circle cx="62" cy="87" r="5"/><circle cx="82" cy="87" r="5"/><circle cx="102" cy="87" r="5"/><circle cx="122" cy="87" r="5"/><circle cx="142" cy="87" r="5"/><circle cx="162" cy="87" r="5"/><circle cx="182" cy="87" r="5"/><circle cx="202" cy="87" r="5"/><circle cx="222" cy="87" r="5"/><circle cx="242" cy="87" r="5"/></g>
              <path class="ship__hull" d="M10 100H290L264 140H40Z"/>
              <path class="ship__stripe" d="M15 108H285L280 116H20Z"/>
            </svg>
        </div>

        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>


      <!-- Clickable destination chips -->
      <ul class="hero__chips" aria-label="{{ __('Destinations') }}">
        @foreach ($destinations as $place)
        <li><a class="hero-chip" href="{{ lroute('destination', $place->slug) }}">@include('partials.icons.destination', ['slug' => $place->slug]){{ $place->name }}</a></li>
        @endforeach
      </ul>

      <!-- Hero content -->
      <div class="container hero__content">
        <p class="hero__badge">{{ __('Curated journeys · Local guides · Direct booking') }}</p>
        <h1 class="hero__title">
          <span class="line"><span class="line__inner">{{ __('Six Places.') }}</span></span>
          <span class="line"><span class="line__inner">{{ __('One') }} <em>{{ __('Unforgettable') }}</em></span></span>
          <span class="line"><span class="line__inner">{{ __('Bangladesh.') }}</span></span>
        </h1>
        <p class="hero__subtitle">{{ __('Hand-planned tours to the country\'s most beautiful destinations, with local guides, honest prices and zero stress.') }}</p>
        <div class="hero__actions">
          <a href="#destinations" class="btn btn--primary btn--lg">{{ __('Explore Destinations') }}</a>
          <a href="#packages" class="btn btn--ghost btn--lg">{{ __('View Packages') }}</a>
        </div>
        <p class="hero__trust"><span class="stars" aria-hidden="true">★★★★★</span><span>{{ __('4.9 rating · 5,000+ happy travelers') }}</span></p>
      </div>

      <!-- Search bar -->
      <form class="container hero__search" id="hero-search" aria-label="{{ __('Search tours') }}">
        <div class="search__field">
          <label for="search-destination">{{ __('Where') }}</label>
          <select id="search-destination" name="destination">
            <option value="">{{ __('Choose destination') }}</option>
            @foreach ($destinations as $place)
            <option value="{{ $place->getTranslation('name', 'en') }}">{{ $place->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="search__field">
          <label for="search-date">{{ __('When') }}</label>
          <input type="date" id="search-date" name="date">
        </div>
        <div class="search__field">
          <label for="search-guests">{{ __('Guests') }}</label>
          <select id="search-guests" name="guests">
            <option value="1">{{ __('1 Guest') }}</option>
            <option value="2" selected>{{ __('2 Guests') }}</option>
            <option value="3">{{ __('3 Guests') }}</option>
            <option value="4">{{ __('4 Guests') }}</option>
            <option value="5+">{{ __('5+ Guests') }}</option>
          </select>
        </div>
        <button type="submit" class="btn btn--primary search__btn">{{ __('Search') }}</button>
      </form>
    </section>


    <!-- =====================================================
         3. DESTINATIONS (6 places)
         ===================================================== -->
    <section class="section" id="destinations">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Destinations') }}</p>
          <h2 class="section__title">{{ __('Six places worth the journey') }}</h2>
          <p class="section__lead">{{ __('From the world\'s longest sea beach to the largest mangrove forest on Earth.') }}</p>
        </header>

        <div class="grid grid--3">
          @foreach ($destinations as $place)
          @include('partials.cards.destination', ['destination' => $place, 'text' => 'summary'])
          @endforeach
        </div>
      </div>
    </section>


    <!-- =====================================================
         NOTICE BAR
         ===================================================== -->
    <section class="announce" aria-label="{{ __('Notice') }}">
      <div class="container announce__inner">
        <span class="announce__pulse" aria-hidden="true"></span>
        <p><strong>{{ __('Notice:') }}</strong> {{ __('Peak season bookings (November to March) are now open. Dates and ship timings for Saint Martin\'s can change, so message us for the latest.') }}</p>
        <a href="{{ lroute('packages') }}" class="btn btn--small">{{ __('Book early') }}</a>
      </div>
    </section>


    <!-- =====================================================
         4. PACKAGES
         ===================================================== -->
    <section class="section section--alt" id="packages">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Packages') }}</p>
          <h2 class="section__title">{{ __('Choose how you want to travel') }}</h2>
          <p class="section__lead">{{ __('Transparent pricing per person. No hidden fees.') }}</p>
        </header>

        <div class="grid grid--3 packages">

          @foreach ($packages as $package)
          <article class="package{{ $package->is_featured ? ' package--featured' : '' }}" data-reveal>
            @if ($package->badge)
            <span class="package__badge">{{ $package->badge }}</span>
            @endif
            <h3 class="package__name">{{ $package->name }}</h3>
            <p class="package__desc">{{ $package->description }}</p>
            <p class="package__price"><span class="package__currency">৳</span>{{ format_number($package->price) }}<small> {{ __('/ person') }}</small></p>
            <ul class="package__list">
              @foreach ($package->features as $feature)
              <li>{{ $feature }}</li>
              @endforeach
            </ul>
            <a href="#booking" class="btn {{ $package->is_featured ? 'btn--primary' : 'btn--outline' }} btn--block" data-package="{{ $package->getTranslation('name', 'en') }}">{{ __('Choose plan') }}</a>
          </article>
          @endforeach


        </div>

        <p class="section__more" data-reveal>
          <a href="{{ lroute('packages') }}" class="link-arrow">{{ __('Compare all packages and add-ons') }} <span aria-hidden="true">→</span></a>
        </p>
      </div>
    </section>


    <!-- =====================================================
         5. WHY CHOOSE US
         ===================================================== -->
    <section class="section" id="why-us">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Why TravelOrio') }}</p>
          <h2 class="section__title">{{ __('Travel with people who know the way') }}</h2>
        </header>

        <div class="grid grid--4 features">

          <div class="feature" data-reveal>
            <div class="feature__icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
            </div>
            <h3 class="feature__title">{{ __('Local Expert Guides') }}</h3>
            <p class="feature__text">{{ __('Born and raised in the regions you visit, they know the hidden spots.') }}</p>
          </div>

          <div class="feature" data-reveal>
            <div class="feature__icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M14.8 9.2c-.6-.8-1.6-1.2-2.8-1.2-1.7 0-2.8.9-2.8 2 0 3 5.6 1.4 5.6 4.2 0 1.2-1.2 2-2.8 2-1.3 0-2.3-.5-3-1.4M12 6v2m0 8v2"/></svg>
            </div>
            <h3 class="feature__title">{{ __('Honest Pricing') }}</h3>
            <p class="feature__text">{{ __('One clear price per person. What you see is what you pay.') }}</p>
          </div>

          <div class="feature" data-reveal>
            <div class="feature__icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/></svg>
            </div>
            <h3 class="feature__title">{{ __('Safe and Insured') }}</h3>
            <p class="feature__text">{{ __('Vetted hotels, licensed boats and 24/7 trip monitoring.') }}</p>
          </div>

          <div class="feature" data-reveal>
            <div class="feature__icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L3 21l2-5.2A8 8 0 1 1 21 12z"/><path d="M9 10h6M9 14h4"/></svg>
            </div>
            <h3 class="feature__title">{{ __('WhatsApp Support') }}</h3>
            <p class="feature__text">{{ __('Reach a real person any time, before and during your trip.') }}</p>
          </div>

        </div>

        <div class="stats" data-reveal>
          <div class="stat"><span class="stat__num" data-count="5000">5,000</span><span class="stat__label">{{ __('Happy travelers') }}</span></div>
          <div class="stat"><span class="stat__num" data-count="6">6</span><span class="stat__label">{{ __('Destinations') }}</span></div>
          <div class="stat"><span class="stat__num" data-count="8">8</span><span class="stat__label">{{ __('Years of experience') }}</span></div>
          <div class="stat"><span class="stat__num" data-count="4.9" data-decimals="1">4.9</span><span class="stat__label">{{ __('Average rating') }}</span></div>
        </div>
      </div>
    </section>


    <!-- =====================================================
         6. HOW IT WORKS
         ===================================================== -->
    <section class="section section--alt" id="how-it-works">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('How It Works') }}</p>
          <h2 class="section__title">{{ __('Booking takes three simple steps') }}</h2>
        </header>

        <ol class="steps">
          <li class="step" data-reveal>
            <span class="step__num">01</span>
            <h3 class="step__title">{{ __('Choose your destination') }}</h3>
            <p class="step__text">{{ __('Pick a place and a package that fits your time and budget.') }}</p>
          </li>
          <li class="step" data-reveal>
            <span class="step__num">02</span>
            <h3 class="step__title">{{ __('Send your request') }}</h3>
            <p class="step__text">{{ __('Fill in the short form or message us on WhatsApp with your dates.') }}</p>
          </li>
          <li class="step" data-reveal>
            <span class="step__num">03</span>
            <h3 class="step__title">{{ __('Confirm and travel') }}</h3>
            <p class="step__text">{{ __('We confirm everything within hours. Pack your bag, we handle the rest.') }}</p>
          </li>
        </ol>
      </div>
    </section>


    <!-- =====================================================
         7. REVIEWS
         ===================================================== -->
    <section class="section" id="reviews">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Reviews') }}</p>
          <h2 class="section__title">{{ __('Loved by travelers') }}</h2>
        </header>

        <div class="grid grid--3">

          @foreach ($stories as $review)
          <figure class="review" data-reveal>
            <div class="review__stars" aria-label="{{ t('{n} out of 5 stars', ['n' => to_locale_digits($review->rating)]) }}">{{ stars($review->rating) }}</div>
            <blockquote class="review__text">{{ $review->body }}</blockquote>
            <figcaption class="review__author">
              <img src="{{ placeholder_image('person'.$loop->iteration, 80, 80) }}" alt="" width="44" height="44" loading="lazy">
              <span><strong>{{ $review->name }}</strong><small>{{ $review->city }} · {{ $review->destination?->name }}</small></span>
            </figcaption>
          </figure>
          @endforeach

        </div>
      
        <p class="section__more" data-reveal>
          <a href="{{ lroute('reviews') }}" class="link-arrow">{{ __('Read all traveler reviews') }} <span aria-hidden="true">→</span></a>
        </p>
      </div>
    </section>


    <!-- =====================================================
         LATEST ARTICLES
         ===================================================== -->
    <section class="section section--alt" id="blog">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('From the blog') }}</p>
          <h2 class="section__title">{{ __('Travel stories and tips') }}</h2>
          <p class="section__lead">{{ __('Plan smarter with advice from our local guides.') }}</p>
        </header>

        <div class="grid grid--3" id="home-blog">
          @foreach ($posts as $post)
          @include('partials.cards.post', ['post' => $post])
          @endforeach
        </div>

        <p class="section__more" data-reveal>
          <a href="{{ lroute('blog') }}" class="link-arrow">{{ __('Read the full blog') }} <span aria-hidden="true">→</span></a>
        </p>
      </div>
    </section>


    <!-- =====================================================
         8. BOOKING / CONTACT
         ===================================================== -->
    <section class="section" id="booking">
      <div class="container booking">

        <div class="booking__info" data-reveal>
          <p class="eyebrow">{{ __('Book Your Trip') }}</p>
          <h2 class="section__title">{{ __('Tell us where you want to go') }}</h2>
          <p class="section__lead">{{ __('Send a request and we\'ll reply with a personalised quote within a few hours.') }}</p>

          <ul class="contact-list" id="contact">
            <li>
              <span class="contact-list__label">{{ __('WhatsApp') }}</span>
              <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener">{{ site('phone_display') }}</a>
            </li>
            <li>
              <span class="contact-list__label">{{ __('Email') }}</span>
              <a href="mailto:{{ site('email') }}">{{ site('email') }}</a>
            </li>
            <li>
              <span class="contact-list__label">{{ __('Office') }}</span>
              <span>{{ __('House 12, Road 5, Dhanmondi, Dhaka') }}</span>
            </li>
            <li>
              <span class="contact-list__label">{{ __('Hours') }}</span>
              <span>{{ __('Sat–Thu, 9:00 AM – 9:00 PM') }}</span>
            </li>
          </ul>
        </div>

        <form method="post" action="{{ lurl('inquiries/booking') }}" class="booking__form" id="booking-form" novalidate data-reveal>
          @csrf
          <input type="hidden" name="_fragment" value="booking">
          <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <div class="form-row">
            <div class="form-field{{ $errors->has('name') ? ' has-error' : '' }}">
              <label for="b-name">{{ __('Full name') }}</label>
              <input type="text" id="b-name" name="name" placeholder="{{ __('Your name') }}" required autocomplete="name" value="{{ old('name') }}">
              <small class="form-error" aria-live="polite">{{ $errors->first('name') }}</small>
            </div>
            <div class="form-field{{ $errors->has('phone') ? ' has-error' : '' }}">
              <label for="b-phone">{{ __('Phone / WhatsApp') }}</label>
              <input type="tel" id="b-phone" name="phone" placeholder="{{ __('+880 1XXX-XXXXXX') }}" required autocomplete="tel" value="{{ old('phone') }}">
              <small class="form-error" aria-live="polite">{{ $errors->first('phone') }}</small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field{{ $errors->has('destination') ? ' has-error' : '' }}">
              <label for="b-destination">{{ __('Destination') }}</label>
              <select id="b-destination" name="destination" required>
                <option value="" @selected(old('destination') === '')>{{ __('Select destination') }}</option>
                @foreach ($destinations as $place)
                <option value="{{ $place->getTranslation('name', 'en') }}" @selected(old('destination') === $place->getTranslation('name', 'en'))>{{ $place->name }}</option>
                @endforeach
              </select>
              <small class="form-error" aria-live="polite">{{ $errors->first('destination') }}</small>
            </div>
            <div class="form-field{{ $errors->has('package') ? ' has-error' : '' }}">
              <label for="b-package">{{ __('Package') }}</label>
              <select id="b-package" name="package">
                <option value="" @selected(old('package') === '')>{{ __('Not sure yet') }}</option>
                @foreach ($packages as $package)
                <option value="{{ $package->getTranslation('name', 'en') }}" @selected(old('package') === $package->getTranslation('name', 'en'))>{{ $package->name }}</option>
                @endforeach
              </select>
              <small class="form-error" aria-hidden="true">{{ $errors->first('package') }}</small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field{{ $errors->has('date') ? ' has-error' : '' }}">
              <label for="b-date">{{ __('Travel date') }}</label>
              <input type="date" id="b-date" name="date" required value="{{ old('date') }}">
              <small class="form-error" aria-live="polite">{{ $errors->first('date') }}</small>
            </div>
            <div class="form-field{{ $errors->has('guests') ? ' has-error' : '' }}">
              <label for="b-guests">{{ __('Number of travelers') }}</label>
              <input type="number" id="b-guests" name="guests" min="1" max="50" value="{{ old('guests', '2') }}" required>
              <small class="form-error" aria-live="polite">{{ $errors->first('guests') }}</small>
            </div>
          </div>

          <div class="form-field{{ $errors->has('message') ? ' has-error' : '' }}">
            <label for="b-message">{{ __('Message') }} <span class="optional">{{ __('(optional)') }}</span></label>
            <textarea id="b-message" name="message" rows="4" placeholder="{{ __('Anything we should know? Dietary needs, special occasion, budget…') }}">{{ old('message') }}</textarea>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary btn--lg">{{ __('Send via WhatsApp') }}</button>
            <p class="form-note">{{ __('We never share your details. No payment is taken at this step.') }}</p>
          </div>

          <p class="form-success" id="form-success" role="status" hidden>{{ __('Thank you! Your request is ready. Complete it in WhatsApp and we\'ll reply shortly.') }}</p>
        </form>

      </div>
    </section>

  
@endsection
