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

          @foreach ($packages as $package)
          <article class="package{{ $package->is_featured ? ' package--featured' : '' }}" data-reveal>
            @if ($package->badge)
            <span class="package__badge">{{ $package->badge }}</span>
            @endif
            <h3 class="package__name">{{ $package->name }}</h3>
            <p class="package__desc">{{ $package->description }}</p>
            <p class="package__best">{{ $package->best_for }}</p>
            <p class="package__price"><span class="package__currency">৳</span>{{ format_number($package->price) }}<small> {{ __('/ person') }}</small></p>
            <ul class="package__list">
              @foreach ($package->features as $feature)
              <li>{{ $feature }}</li>
              @endforeach
            </ul>
            <a href="#booking" class="btn {{ $package->is_featured ? 'btn--primary' : 'btn--outline' }} btn--block" data-package="{{ $package->getTranslation('name', 'en') }}">{{ t('Choose {name}', ['name' => $package->name]) }}</a>
          </article>
          @endforeach

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
                @foreach ($packages as $package)
                <th scope="col"{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ $package->name }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              <tr><th scope="row">{{ __('Price per person') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ format_money($package->price) }}</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Duration') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ t('{n} days', ['n' => to_locale_digits($package->days)]) }}</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Destinations') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ to_locale_digits($package->destinations_count) }}</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Hotel') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ $package->hotel }}</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Meals') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ $package->meals }}</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Transport') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ $package->transport }}</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Local guide') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>@if ($package->has_guide)<span class="yes" aria-label="{{ __('Included') }}">✓</span>@else<span class="no" aria-label="{{ __('Not included') }}">—</span>@endif</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Entry tickets and activities') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>@if ($package->has_tickets)<span class="yes" aria-label="{{ __('Included') }}">✓</span>@else<span class="no" aria-label="{{ __('Not included') }}">—</span>@endif</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Airport pickup and drop') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>@if ($package->has_airport_transfer)<span class="yes" aria-label="{{ __('Included') }}">✓</span>@else<span class="no" aria-label="{{ __('Not included') }}">—</span>@endif</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Dedicated trip manager') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>@if ($package->has_trip_manager)<span class="yes" aria-label="{{ __('Included') }}">✓</span>@else<span class="no" aria-label="{{ __('Not included') }}">—</span>@endif</td>@endforeach</tr>
              <tr><th scope="row">{{ __('Free cancellation') }}</th>@foreach ($packages as $package)<td{!! $package->is_featured ? ' class="compare__featured"' : '' !!}>{{ $package->cancellation }}</td>@endforeach</tr>
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
          @foreach ($destinations as $place)
          @include('partials.cards.destination', ['destination' => $place, 'text' => 'tagline'])
          @endforeach
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

          @foreach ($addons as $addon)
          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              @include('partials.icons.addon', ['icon' => $addon->icon])
            </div>
            <h3 class="addon__title">{{ $addon->title }}</h3>
            <p class="addon__text">{{ $addon->description }}</p>
            <p class="addon__price">{{ t('from {price}', ['price' => format_money($addon->price_from)]) }}@if ($addon->price_unit) {{ $addon->price_unit }}@endif</p>
          </div>
          @endforeach

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

        <form method="post" action="{{ lurl('inquiries/booking') }}" class="booking__form" id="booking-form" novalidate data-reveal>
          @csrf
          <input type="hidden" name="_fragment" value="booking">
          <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <input type="hidden" name="estimate" id="b-estimate" value="">

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
            <div class="form-field{{ $errors->has('package') ? ' has-error' : '' }}">
              <label for="b-package">{{ __('Plan') }}</label>
              <select id="b-package" name="package">
                <option value="" @selected(old('package') === '') data-price="">{{ __('Not sure yet') }}</option>
                @foreach ($packages as $package)
                <option value="{{ $package->getTranslation('name', 'en') }}" @selected(old('package') === $package->getTranslation('name', 'en')) data-price="{{ $package->price }}">{{ $package->name }} · {{ format_money($package->price) }}</option>
                @endforeach
              </select>
              <small class="form-error" aria-hidden="true">{{ $errors->first('package') }}</small>
            </div>
            <div class="form-field{{ $errors->has('destination') ? ' has-error' : '' }}">
              <label for="b-destination">{{ __('Destination') }}</label>
              <select id="b-destination" name="destination" required>
                <option value="" @selected(old('destination') === '')>{{ __('Select destination') }}</option>
                @foreach ($destinations as $place)
                <option value="{{ $place->getTranslation('name', 'en') }}" @selected(old('destination') === $place->getTranslation('name', 'en')) data-slug="{{ $place->slug }}">{{ $place->name }}</option>
                @endforeach
              </select>
              <small class="form-error" aria-live="polite">{{ $errors->first('destination') }}</small>
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
            <textarea id="b-message" name="message" rows="4" placeholder="{{ __('Add-ons, dietary needs, special occasion, budget…') }}">{{ old('message') }}</textarea>
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

@push('scripts')
  <script src="{{ asset_js('packages.js') }}" defer></script>
@endpush
