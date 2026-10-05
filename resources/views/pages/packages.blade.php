@extends('layouts.app', [
  'title' => 'Tour Packages & Pricing',
  'description' => 'Compare TravelOrio tour packages for Bangladesh: Weekend Escape, Explorer and Grand Bangladesh. Transparent per-person pricing, add-ons and instant WhatsApp booking.',
  'bodyClass' => 'page-packages',
])

@section('content')
    <!-- =====================================================
         1. PAGE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="{{ __('Packages introduction') }}">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>

      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
          <ol>
            <li><a href="{{ lroute('home') }}">{{ __('Home') }}</a></li>
            <li aria-current="page">{{ __('Packages') }}</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow">{{ __('Packages & Pricing') }}</p>
        <h1 class="page-hero__title">{{ __('Pick a plan.') }} <em>{{ __('We\'ll plan the rest.') }}</em></h1>
        <p class="page-hero__lead">{{ __('Three clear options for exploring Bangladesh, priced per person with no hidden fees. Mix and match any of our six destinations.') }}</p>
        <div class="page-hero__actions">
          <a href="#plans" class="btn btn--primary btn--lg">{{ __('See the Plans') }}</a>
          <a href="#compare" class="btn btn--ghost btn--lg">{{ __('Compare Features') }}</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. PLANS
         ===================================================== -->
    <section class="section" id="plans">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Our plans') }}</p>
          <h2 class="section__title">{{ __('Choose how you want to travel') }}</h2>
          <p class="section__lead">{{ __('Prices are per person for twin-sharing. Your final quote depends on the destination, season and hotel category.') }}</p>
        </header>

        <div class="grid grid--3 packages">

          <article class="package" data-reveal>
            <h3 class="package__name">{{ __('Weekend Escape') }}</h3>
            <p class="package__desc">{{ __('A quick, restful getaway.') }}</p>
            <p class="package__best">{{ __('Best for couples and short breaks') }}</p>
            <p class="package__price"><span class="package__currency">৳</span>{{ __('5,500') }}<small> {{ __('/ person') }}</small></p>
            <ul class="package__list">
              <li>{{ __('2 days, 1 night') }}</li>
              <li>{{ __('Standard hotel stay') }}</li>
              <li>{{ __('Breakfast included') }}</li>
              <li>{{ __('Local transport') }}</li>
              <li>{{ __('1 destination') }}</li>
            </ul>
            <a href="#booking" class="btn btn--outline btn--block" data-package="Weekend Escape">{{ __('Choose Weekend Escape') }}</a>
          </article>

          <article class="package package--featured" data-reveal>
            <span class="package__badge">{{ __('Most Popular') }}</span>
            <h3 class="package__name">{{ __('Explorer') }}</h3>
            <p class="package__desc">{{ __('Our complete experience.') }}</p>
            <p class="package__best">{{ __('Best for families and friends') }}</p>
            <p class="package__price"><span class="package__currency">৳</span>{{ __('12,500') }}<small> {{ __('/ person') }}</small></p>
            <ul class="package__list">
              <li>{{ __('4 days, 3 nights') }}</li>
              <li>{{ __('Premium hotel or resort') }}</li>
              <li>{{ __('All meals included') }}</li>
              <li>{{ __('Private transport and guide') }}</li>
              <li>{{ __('Entry tickets and activities') }}</li>
            </ul>
            <a href="#booking" class="btn btn--primary btn--block" data-package="Explorer">{{ __('Choose Explorer') }}</a>
          </article>

          <article class="package" data-reveal>
            <h3 class="package__name">{{ __('Grand Bangladesh') }}</h3>
            <p class="package__desc">{{ __('Multiple destinations in one trip.') }}</p>
            <p class="package__best">{{ __('Best for first-time visitors') }}</p>
            <p class="package__price"><span class="package__currency">৳</span>{{ __('28,000') }}<small> {{ __('/ person') }}</small></p>
            <ul class="package__list">
              <li>{{ __('8 days, 7 nights') }}</li>
              <li>{{ __('3 destinations of your choice') }}</li>
              <li>{{ __('Luxury stays') }}</li>
              <li>{{ __('Dedicated trip manager') }}</li>
              <li>{{ __('Airport pickup and drop') }}</li>
            </ul>
            <a href="#booking" class="btn btn--outline btn--block" data-package="Grand Bangladesh">{{ __('Choose Grand Bangladesh') }}</a>
          </article>

        </div>
      </div>
    </section>


    <!-- =====================================================
         3. COMPARE TABLE
         ===================================================== -->
    <section class="section section--alt" id="compare">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Compare') }}</p>
          <h2 class="section__title">{{ __('What\'s in each plan') }}</h2>
        </header>

        <p class="compare-hint" aria-hidden="true">{{ __('Swipe sideways to compare plans →') }}</p>
        <div class="compare-wrap" data-reveal>
          <table class="compare">
            <caption class="sr-only">{{ __('Feature comparison of TravelOrio packages') }}</caption>
            <thead>
              <tr>
                <th scope="col"><span class="sr-only">{{ __('Feature') }}</span></th>
                <th scope="col">{{ __('Weekend Escape') }}</th>
                <th scope="col" class="compare__featured">{{ __('Explorer') }}</th>
                <th scope="col">{{ __('Grand Bangladesh') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr><th scope="row">{{ __('Price per person') }}</th><td>{{ __('৳5,500') }}</td><td class="compare__featured">{{ __('৳12,500') }}</td><td>{{ __('৳28,000') }}</td></tr>
              <tr><th scope="row">{{ __('Duration') }}</th><td>{{ __('2 days') }}</td><td class="compare__featured">{{ __('4 days') }}</td><td>{{ __('8 days') }}</td></tr>
              <tr><th scope="row">{{ __('Destinations') }}</th><td>{{ to_locale_digits('1') }}</td><td class="compare__featured">{{ to_locale_digits('1') }}</td><td>{{ to_locale_digits('3') }}</td></tr>
              <tr><th scope="row">{{ __('Hotel') }}</th><td>{{ __('Standard') }}</td><td class="compare__featured">{{ __('Premium') }}</td><td>{{ __('Luxury') }}</td></tr>
              <tr><th scope="row">{{ __('Meals') }}</th><td>{{ __('Breakfast') }}</td><td class="compare__featured">{{ __('All meals') }}</td><td>{{ __('All meals') }}</td></tr>
              <tr><th scope="row">{{ __('Transport') }}</th><td>{{ __('Local') }}</td><td class="compare__featured">{{ __('Private AC vehicle') }}</td><td>{{ __('Private AC vehicle') }}</td></tr>
              <tr><th scope="row">{{ __('Local guide') }}</th><td><span class="no" aria-label="{{ __('Not included') }}">—</span></td><td class="compare__featured"><span class="yes" aria-label="{{ __('Included') }}">✓</span></td><td><span class="yes" aria-label="{{ __('Included') }}">✓</span></td></tr>
              <tr><th scope="row">{{ __('Entry tickets and activities') }}</th><td><span class="no" aria-label="{{ __('Not included') }}">—</span></td><td class="compare__featured"><span class="yes" aria-label="{{ __('Included') }}">✓</span></td><td><span class="yes" aria-label="{{ __('Included') }}">✓</span></td></tr>
              <tr><th scope="row">{{ __('Airport pickup and drop') }}</th><td><span class="no" aria-label="{{ __('Not included') }}">—</span></td><td class="compare__featured"><span class="no" aria-label="{{ __('Not included') }}">—</span></td><td><span class="yes" aria-label="{{ __('Included') }}">✓</span></td></tr>
              <tr><th scope="row">{{ __('Dedicated trip manager') }}</th><td><span class="no" aria-label="{{ __('Not included') }}">—</span></td><td class="compare__featured"><span class="no" aria-label="{{ __('Not included') }}">—</span></td><td><span class="yes" aria-label="{{ __('Included') }}">✓</span></td></tr>
              <tr><th scope="row">{{ __('Free cancellation') }}</th><td>{{ __('7 days before') }}</td><td class="compare__featured">{{ __('7 days before') }}</td><td>{{ __('7 days before') }}</td></tr>
            </tbody>
          </table>
        </div>
        <p class="compare-note" data-reveal>{{ __('Need something different? Every plan can be customised. Tell us what you\'d like to change in the request form below.') }}</p>
      </div>
    </section>


    <!-- =====================================================
         4. TRIPS BY DESTINATION (rendered from js/data.js)
         ===================================================== -->
    <section class="section" id="by-destination">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('By destination') }}</p>
          <h2 class="section__title">{{ __('Or start with a place') }}</h2>
          <p class="section__lead">{{ __('Each trip has its own itinerary and starting price. Pick one to see the full plan.') }}</p>
        </header>

        <div class="grid grid--3" id="destination-cards">
          <noscript>
            <p>Browse destinations on the <a href="{{ lroute('home') }}#destinations">home page</a>.</p>
          </noscript>
        </div>
      </div>
    </section>


    <!-- =====================================================
         5. ADD-ONS
         ===================================================== -->
    <section class="section section--alt" id="addons">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Optional extras') }}</p>
          <h2 class="section__title">{{ __('Make it your own') }}</h2>
          <p class="section__lead">{{ __('Add any of these to a plan. Mention them in your request and we\'ll include them in the quote.') }}</p>
        </header>

        <div class="grid grid--4 addons">

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.300.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            </div>
            <h3 class="addon__title">{{ __('Airport transfer') }}</h3>
            <p class="addon__text">{{ __('Private pickup and drop in an AC car.') }}</p>
            <p class="addon__price">{{ __('from ৳1,500') }}</p>
          </div>

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 1 2-2h2l1.5-2h7L17 6h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><circle cx="12" cy="13" r="3.5"/></svg>
            </div>
            <h3 class="addon__title">{{ __('Trip photographer') }}</h3>
            <p class="addon__text">{{ __('A professional to capture your best moments.') }}</p>
            <p class="addon__price">{{ __('from ৳4,000 / day') }}</p>
          </div>

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l2 3h14l2-3z"/><path d="M12 3v11M12 4l6 8h-6M12 6L7 12h5"/></svg>
            </div>
            <h3 class="addon__title">{{ __('Private boat') }}</h3>
            <p class="addon__text">{{ __('Skip the crowds with your own boat and crew.') }}</p>
            <p class="addon__price">{{ __('from ৳3,500') }}</p>
          </div>

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20V8M3 14h18v6M21 14v-2a3 3 0 0 0-3-3h-7v5"/><circle cx="7" cy="11" r="1.5"/></svg>
            </div>
            <h3 class="addon__title">{{ __('Extra night') }}</h3>
            <p class="addon__text">{{ __('Stay longer at the same hotel and rate.') }}</p>
            <p class="addon__price">{{ __('from ৳2,800 / room') }}</p>
          </div>

        </div>
      </div>
    </section>


    <!-- =====================================================
         6. GROUP / CUSTOM TRIP BANNER
         ===================================================== -->
    <section class="cta-band" aria-label="{{ __('Group and custom trips') }}">
      <div class="container cta-band__inner" data-reveal>
        <div>
          <h2 class="cta-band__title">{!! __('Travelling with <em>6 or more?</em>') !!}</h2>
          <p>{{ __('Groups, families and corporate tours get a reduced per-person price and a dedicated trip manager.') }}</p>
        </div>
        <a href="{{ whatsapp_url() }}?text=Hello%20TravelOrio!%20I'd%20like%20a%20group%20quote." class="btn btn--primary btn--lg" target="_blank" rel="noopener">{{ __('Get a Group Quote') }}</a>
      </div>
    </section>


    <!-- =====================================================
         7. FAQ
         ===================================================== -->
    <section class="section" id="faq">
      <div class="container container--narrow">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Pricing FAQ') }}</p>
          <h2 class="section__title">{{ __('Questions about packages') }}</h2>
        </header>

        <div class="faq" data-reveal>
          <details class="faq__item">
            <summary>{{ __('What does "per person" mean?') }}</summary>
            <p>{{ __('Prices are for each traveler based on two people sharing a room. Single travelers pay a small single-supplement, which we show in your quote.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Why do prices vary by destination?') }}</summary>
            <p>{{ __('Hotels, transport and entry fees cost different amounts in each region. The plan prices above are typical starting points, and the exact figure appears in your personalised quote.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('How do I pay?') }}</summary>
            <p>{{ __('No payment is taken online. After you send a request we confirm availability and share bKash, Nagad or bank transfer details. A deposit holds your booking and the balance is due before departure.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Can I change my plan after booking?') }}</summary>
            <p>{{ __('Yes, as long as hotels and transport allow it. Tell us as early as you can and we will rebook without extra fees wherever possible.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('What is your cancellation policy?') }}</summary>
            <p>{{ __('Free cancellation up to 7 days before departure. After that, charges depend on hotel and transport bookings already made.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Are flights and bus tickets included?') }}</summary>
            <p>{{ __('Long-distance tickets are not included in the plan prices, but we can book them for you at the actual cost.') }}</p>
          </details>
        </div>
      </div>
    </section>


    <!-- =====================================================
         8. BOOKING REQUEST
         ===================================================== -->
    <section class="section section--alt" id="booking">
      <div class="container booking">

        <div class="booking__info" data-reveal>
          <p class="eyebrow">{{ __('Request a quote') }}</p>
          <h2 class="section__title">{{ __('Tell us your plan') }}</h2>
          <p class="section__lead">{{ __('Choose a plan and destination, add your dates, and we\'ll reply with a personalised quote within a few hours.') }}</p>

          <div class="estimate" aria-live="polite">
            <p class="estimate__label">{{ __('Estimated total') }}</p>
            <p class="estimate__total" id="estimate-total">{{ __('Choose a plan') }}</p>
            <p class="estimate__note" id="estimate-note">{{ __('A rough guide based on the plan price. Your final quote may differ by destination and season.') }}</p>
          </div>

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
              <span class="contact-list__label">{{ __('Hours') }}</span>
              <span>{{ __('Sat–Thu, 9:00 AM – 9:00 PM') }}</span>
            </li>
          </ul>
        </div>

        <form class="booking__form" id="booking-form" novalidate data-reveal>
          <input type="hidden" name="estimate" id="b-estimate" value="">

          <div class="form-row">
            <div class="form-field">
              <label for="b-name">{{ __('Full name') }}</label>
              <input type="text" id="b-name" name="name" placeholder="{{ __('Your name') }}" required autocomplete="name">
              <small class="form-error" aria-live="polite"></small>
            </div>
            <div class="form-field">
              <label for="b-phone">{{ __('Phone / WhatsApp') }}</label>
              <input type="tel" id="b-phone" name="phone" placeholder="{{ __('+880 1XXX-XXXXXX') }}" required autocomplete="tel">
              <small class="form-error" aria-live="polite"></small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label for="b-package">{{ __('Plan') }}</label>
              <select id="b-package" name="package">
                <option value="" data-price="">{{ __('Not sure yet') }}</option>
                <option value="Weekend Escape" data-price="5500">{{ __('Weekend Escape · ৳5,500') }}</option>
                <option value="Explorer" data-price="12500">{{ __('Explorer · ৳12,500') }}</option>
                <option value="Grand Bangladesh" data-price="28000">{{ __('Grand Bangladesh · ৳28,000') }}</option>
              </select>
              <small class="form-error" aria-hidden="true"></small>
            </div>
            <div class="form-field">
              <label for="b-destination">{{ __('Destination') }}</label>
              <select id="b-destination" name="destination" required>
                <option value="">{{ __('Select destination') }}</option>
                <option value="Cox's Bazar">{{ __('Cox\'s Bazar') }}</option>
                <option value="Sundarbans">{{ __('Sundarbans') }}</option>
                <option value="Sylhet">{{ __('Sylhet') }}</option>
                <option value="Bandarban">{{ __('Bandarban') }}</option>
                <option value="Saint Martin's Island">{{ __('Saint Martin\'s Island') }}</option>
                <option value="Kuakata">{{ __('Kuakata') }}</option>
              </select>
              <small class="form-error" aria-live="polite"></small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label for="b-date">{{ __('Travel date') }}</label>
              <input type="date" id="b-date" name="date" required>
              <small class="form-error" aria-live="polite"></small>
            </div>
            <div class="form-field">
              <label for="b-guests">{{ __('Number of travelers') }}</label>
              <input type="number" id="b-guests" name="guests" min="1" max="50" value="2" required>
              <small class="form-error" aria-live="polite"></small>
            </div>
          </div>

          <div class="form-field">
            <label for="b-message">{{ __('Message') }} <span class="optional">{{ __('(optional)') }}</span></label>
            <textarea id="b-message" name="message" rows="4" placeholder="{{ __('Add-ons, dietary needs, special occasion, budget…') }}"></textarea>
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

@push('scripts-data')
  <script src="{{ asset_js('data.js') }}" defer></script>
@endpush

@push('scripts-i18n')
  <script src="{{ asset_js('i18n/data-bn.js') }}" defer></script>
@endpush

@push('scripts')
  <script src="{{ asset_js('packages.js') }}" defer></script>
@endpush
