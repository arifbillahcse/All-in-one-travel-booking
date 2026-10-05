@extends('layouts.app', [
  'bodyClass' => 'page-destination',
])

@section('content')
    <!-- =====================================================
         1. DESTINATION HERO
         ===================================================== -->
    <section class="dest-hero" id="home" aria-label="{{ __('Destination introduction') }}">
      <div class="dest-hero__media" aria-hidden="true">
        <img src="{{ $destination->hero_image }}" alt="" width="1920" height="1080" fetchpriority="high">
      </div>

      <div class="container dest-hero__content">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
          <ol>
            <li><a href="{{ lroute('home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ lroute('home') }}#destinations">{{ __('Destinations') }}</a></li>
            <li aria-current="page">{{ $destination->name }}</li>
          </ol>
        </nav>

        <p class="eyebrow dest-hero__eyebrow">{{ $destination->region }}</p>
        <h1 class="dest-hero__title">{{ $destination->name }}</h1>
        <p class="dest-hero__tagline">{{ $destination->tagline }}</p>

        <div class="dest-hero__actions">
          <a href="#book" class="btn btn--primary btn--lg">{{ __('Book This Trip') }}</a>
          <a href="#itinerary" class="btn btn--ghost btn--lg">{{ __('View Itinerary') }}</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. QUICK FACTS STRIP
         ===================================================== -->
    <section class="facts" aria-label="{{ __('Quick facts') }}">
      <div class="container">
        <dl class="facts__list">
          <div class="facts__item">
            <dt>{{ __('Starting from') }}</dt>
            <dd>{{ format_money($destination->price_from) }} <small>{{ __('/ person') }}</small></dd>
          </div>
          <div class="facts__item">
            <dt>{{ __('Duration') }}</dt>
            <dd>{{ $destination->duration }}</dd>
          </div>
          <div class="facts__item">
            <dt>{{ __('Best time') }}</dt>
            <dd>{{ $destination->best_time }}</dd>
          </div>
          <div class="facts__item">
            <dt>{{ __('From Dhaka') }}</dt>
            <dd>{{ $destination->distance }}</dd>
          </div>
          <div class="facts__item">
            <dt>{{ __('Trip style') }}</dt>
            <dd>{{ $destination->style }}</dd>
          </div>
        </dl>
      </div>
    </section>


    <!-- =====================================================
         3. OVERVIEW + STICKY BOOKING CARD
         ===================================================== -->
    <section class="section" id="overview">
      <div class="container detail-layout">

        <div class="detail-main">

          <!-- Overview -->
          <div class="detail-block" data-reveal>
            <p class="eyebrow">{{ __('Overview') }}</p>
            <h2 class="section__title">{{ $destination->overview_title }}</h2>
            <div class="prose">
              @foreach ($destination->overview as $paragraph)
              <p>{{ $paragraph }}</p>
              @endforeach
            </div>

            <ul class="tick-list tick-list--2col">
                @foreach ($destination->highlights as $item)
                <li>{{ $item }}</li>
                @endforeach
              </ul>
          </div>

          <!-- Attractions -->
          <div class="detail-block" id="attractions" data-reveal>
            <p class="eyebrow">{{ __('Top attractions') }}</p>
            <h2 class="section__title">{{ __('What you\'ll see') }}</h2>

            <div class="grid grid--2 attractions">
              @foreach ($destination->attractions as $attraction)
              <article class="attraction"><img src="{{ $attraction['img'] }}" alt="{{ $attraction['name'] }}" loading="lazy" width="640" height="420">
                <div class="attraction__body"><h3>{{ $attraction['name'] }}</h3><p>{{ $attraction['text'] }}</p></div></article>
              @endforeach
            </div>
          </div>

          <!-- Itinerary -->
          <div class="detail-block" id="itinerary" data-reveal>
            <p class="eyebrow">{{ __('Itinerary') }}</p>
            <h2 class="section__title">{{ __('Your day-by-day plan') }}</h2>

            <div class="itinerary">
              @foreach ($destination->itinerary as $day)
              <details class="itinerary__day"@if ($loop->first) open @endif><summary>
                <span class="itinerary__num">{{ t('Day {n}', ['n' => to_locale_digits($loop->iteration)]) }}</span>
                <span class="itinerary__title">{{ $day['title'] }}</span></summary>
                <div class="itinerary__body"><ul>@foreach ($day['items'] as $item)<li>{{ $item }}</li>@endforeach</ul></div></details>
              @endforeach
            </div>
          </div>

          <!-- Included / Not included -->
          <div class="detail-block" id="inclusions" data-reveal>
            <p class="eyebrow">{{ __('Inclusions') }}</p>
            <h2 class="section__title">{{ __('What\'s covered') }}</h2>

            <div class="grid grid--2 inclusions">
              <div class="inclusions__col inclusions__col--yes">
                <h3>{{ __('Included') }}</h3>
                <ul class="tick-list">
                @foreach ($destination->included as $item)
                <li>{{ $item }}</li>
                @endforeach
              </ul>
              </div>
              <div class="inclusions__col inclusions__col--no">
                <h3>{{ __('Not included') }}</h3>
                <ul class="cross-list">
                @foreach ($destination->excluded as $item)
                <li>{{ $item }}</li>
                @endforeach
              </ul>
              </div>
            </div>
          </div>

          <!-- Gallery -->
          <div class="detail-block" id="gallery" data-reveal>
            <p class="eyebrow">{{ __('Gallery') }}</p>
            <h2 class="section__title">{{ __('A glimpse of the trip') }}</h2>

            <div class="gallery">
              @foreach ($destination->gallery as $caption)
              @php($wide = $loop->first || $loop->iteration === 6)
              <a class="gallery__item{{ $wide ? ' gallery__item--wide' : '' }}" href="{{ placeholder_image($destination->slug.'-g'.$loop->iteration, $wide ? 1600 : 1200, $wide ? 1000 : 1200) }}">
                <img src="{{ placeholder_image($destination->slug.'-g'.$loop->iteration, $wide ? 800 : 520, 520) }}" alt="{{ $caption }}" loading="lazy" width="{{ $wide ? 800 : 520 }}" height="520"></a>
              @endforeach
            </div>
          </div>

          <!-- When to go -->
          <div class="detail-block" id="best-time" data-reveal>
            <p class="eyebrow">{{ __('When to go') }}</p>
            <h2 class="section__title">{{ __('Best time to visit') }}</h2>

            <div class="seasons">
              @foreach ($destination->seasons as $season)
              <div class="season season--{{ $season['tone'] }}"><span class="season__badge">{{ $season['badge'] }}</span><h3>{{ $season['range'] }}</h3><p>{{ $season['text'] }}</p></div>
              @endforeach
            </div>
          </div>

          <!-- Getting there + tips -->
          <div class="detail-block" id="getting-there" data-reveal>
            <p class="eyebrow">{{ __('Plan ahead') }}</p>
            <h2 class="section__title">{{ __('Getting there and travel tips') }}</h2>

            <div class="grid grid--2 plan">
              <div class="plan__col">
                <h3>{{ __('How to get there') }}</h3>
                <ul class="info-list">
                @foreach ($destination->transport as $item)
                <li>{!! $item !!}</li>
                @endforeach
              </ul>
              </div>
              <div class="plan__col">
                <h3>{{ __('Good to know') }}</h3>
                <ul class="info-list">
                @foreach ($destination->tips as $item)
                <li>{{ $item }}</li>
                @endforeach
              </ul>
              </div>
            </div>
          </div>

          <!-- FAQ -->
          <div class="detail-block" id="faq" data-reveal>
            <p class="eyebrow">{{ __('FAQ') }}</p>
            <h2 class="section__title">{{ __('Common questions') }}</h2>

            <div class="faq">
              @foreach ($destination->faq as $item)
              <details class="faq__item"><summary>{{ $item['q'] }}</summary><p>{{ $item['a'] }}</p></details>
              @endforeach
            </div>
          </div>

        </div><!-- /detail-main -->


        <!-- Sticky booking card -->
        <aside class="detail-side" id="book" aria-label="{{ __('Book this trip') }}">
          <div class="book-card" data-reveal>
            <p class="book-card__from">{{ __('Starting from') }}</p>
            <p class="book-card__price"><span class="package__currency">৳</span>{{ format_number($destination->price_from) }}<small> {{ __('/ person') }}</small></p>
            <p class="book-card__note">{{ __('Free cancellation up to 7 days before departure.') }}</p>

            <form method="post" action="{{ lurl('inquiries/booking') }}" class="book-card__form" id="trip-form" novalidate>
          @csrf
          <input type="hidden" name="_fragment" value="book">
          <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
              <input type="hidden" name="destination" value="{{ $destination->getTranslation('name', 'en') }}">

              <div class="form-field{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="t-name">{{ __('Full name') }}</label>
                <input type="text" id="t-name" name="name" placeholder="{{ __('Your name') }}" required autocomplete="name" value="{{ old('name') }}">
                <small class="form-error" aria-live="polite">{{ $errors->first('name') }}</small>
              </div>

              <div class="form-field{{ $errors->has('phone') ? ' has-error' : '' }}">
                <label for="t-phone">{{ __('Phone / WhatsApp') }}</label>
                <input type="tel" id="t-phone" name="phone" placeholder="{{ __('+880 1XXX-XXXXXX') }}" required autocomplete="tel" value="{{ old('phone') }}">
                <small class="form-error" aria-live="polite">{{ $errors->first('phone') }}</small>
              </div>

              <div class="form-row">
                <div class="form-field{{ $errors->has('date') ? ' has-error' : '' }}">
                  <label for="t-date">{{ __('Travel date') }}</label>
                  <input type="date" id="t-date" name="date" required value="{{ old('date') }}">
                  <small class="form-error" aria-live="polite">{{ $errors->first('date') }}</small>
                </div>
                <div class="form-field{{ $errors->has('guests') ? ' has-error' : '' }}">
                  <label for="t-guests">{{ __('Travelers') }}</label>
                  <input type="number" id="t-guests" name="guests" min="1" max="50" value="{{ old('guests', '2') }}" required>
                  <small class="form-error" aria-live="polite">{{ $errors->first('guests') }}</small>
                </div>
              </div>

              <div class="form-field{{ $errors->has('package') ? ' has-error' : '' }}">
                <label for="t-package">{{ __('Package') }}</label>
                <select id="t-package" name="package">
                  <option value="" @selected(old('package') === '')>{{ __('Not sure yet') }}</option>
                  @foreach ($packages as $package)
                  <option value="{{ $package->getTranslation('name', 'en') }}" @selected(old('package', $package->is_featured ? $package->getTranslation('name', 'en') : '') === $package->getTranslation('name', 'en'))>{{ $package->name }}</option>
                  @endforeach
                </select>
                <small class="form-error" aria-hidden="true">{{ $errors->first('package') }}</small>
              </div>

              <button type="submit" class="btn btn--primary btn--block btn--lg">{{ __('Send via WhatsApp') }}</button>
              <p class="form-note">{{ __('No payment is taken at this step.') }}</p>
              <p class="form-success" id="trip-success" role="status" hidden>{{ __('Thank you! Complete your request in WhatsApp and we\'ll reply shortly.') }}</p>
            </form>

            <ul class="book-card__trust">
              <li>{{ __('Reply within a few hours') }}</li>
              <li>{{ __('Local guides, vetted hotels') }}</li>
              <li>{{ __('No hidden fees') }}</li>
            </ul>
          </div>
        </aside>

      </div>
    </section>


    <!-- =====================================================
         4. OTHER DESTINATIONS
         ===================================================== -->
    <section class="section section--alt" id="more-destinations">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Keep exploring') }}</p>
          <h2 class="section__title">{{ __('More places to love') }}</h2>
          <p class="section__lead">{{ __('Combine two destinations in our Grand Bangladesh package.') }}</p>
        </header>

        <div class="grid grid--3">
          @foreach ($related as $place)
          @include('partials.cards.destination', ['destination' => $place, 'linkLabel' => 'View details'])
          @endforeach
        </div>
      </div>
    </section>


    <!-- =====================================================
         5. CLOSING CTA
         ===================================================== -->
    <section class="cta-band" aria-label="{{ __('Ready to book') }}">
      <div class="container cta-band__inner" data-reveal>
        <div>
          <h2 class="cta-band__title">{!! t('Ready for your {name} escape?', ['name' => '<em>'.e($destination->name).'</em>']) !!}</h2>
          <p>{{ __('Message us and we\'ll build a plan around your dates and budget.') }}</p>
        </div>
        <a href="{{ whatsapp_url(t("Hello TravelOrio! I'm interested in a {name} trip.", ['name' => $destination->name])) }}" class="btn btn--primary btn--lg" target="_blank" rel="noopener">{{ __('Chat on WhatsApp') }}</a>
      </div>
    </section>
@endsection

@push('scripts')
  <script src="{{ asset_js('destination.js') }}" defer></script>
@endpush
