@extends('layouts.app', [
  'title' => 'Traveler Reviews',
  'description' => 'Read verified reviews from travelers who explored Bangladesh with TravelOrio, and share your own experience.',
  'bodyClass' => 'page-reviews',
])

@section('content')
    <!-- =====================================================
         1. PAGE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="{{ __('Reviews introduction') }}">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>
      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
          <ol>
            <li><a href="{{ lroute('home') }}">{{ __('Home') }}</a></li>
            <li aria-current="page">{{ __('Reviews') }}</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow">{{ __('Traveler reviews') }}</p>
        <h1 class="page-hero__title">{{ __('Real trips.') }} <em>{{ __('Real words.') }}</em></h1>
        <p class="page-hero__lead">{{ __('Read what travelers say about their journeys across Bangladesh, then share your own story.') }}</p>
        <div class="page-hero__actions">
          <a href="#all-reviews" class="btn btn--primary btn--lg">{{ __('Read Reviews') }}</a>
          <a href="#write" class="btn btn--ghost btn--lg">{{ __('Write a Review') }}</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. RATING SUMMARY + FEATURED STORY
         ===================================================== -->
    <section class="section section--tight" id="summary">
      <div class="container summary">

        <div class="rating-card" id="rating-card" data-reveal>
          <p class="rating-card__label">{{ __('Overall rating') }}</p>
          <p class="rating-card__score"><span id="rating-avg">{{ format_number($average, 1) }}</span><small>/ 5</small></p>
          <p class="rating-card__stars" id="rating-stars" aria-hidden="true">{{ stars((int) round($average)) }}</p>
          <p class="rating-card__count" id="rating-count">{{ t($total === 1 ? 'Based on {n} traveler review' : 'Based on {n} traveler reviews', ['n' => to_locale_digits($total)]) }}</p>
          <ul class="rating-bars" id="rating-bars" aria-label="{{ __('Rating breakdown') }}">
            @foreach ([5, 4, 3, 2, 1] as $star)
            @php($pct = $total ? (int) round((($breakdown[$star] ?? 0) / $total) * 100) : 0)
            <li><span class="rating-bars__label">{{ to_locale_digits($star) }} ★</span><span class="rating-bars__track"><span class="rating-bars__fill" style="width:{{ $pct }}%"></span></span><span class="rating-bars__pct">{{ to_locale_digits($pct) }}%</span></li>
            @endforeach
          </ul>
        </div>

        <figure class="featured-quote" id="featured-quote" data-reveal>
          <svg class="featured-quote__mark" width="48" height="48" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M9.5 6C6.5 7 4 9.8 4 13.5V18h6v-6H7c0-2 1-3.500 3-4.200zm9 0c-3 1-5.500 3.800-5.500 7.500V18h6v-6h-3c0-2 1-3.500 3-4.200z"/></svg>
          @if ($featured)
          <blockquote id="featured-text">{{ $featured->body }}</blockquote>
          <figcaption id="featured-author"><strong>{{ $featured->name }}</strong> · {{ $featured->city }}<br><span>{{ $featured->destination?->name }} · {{ __($featured->traveler_type) }}</span></figcaption>
          @endif
        </figure>

      </div>
    </section>


    <!-- =====================================================
         3. ALL REVIEWS
         ===================================================== -->
    <section class="section section--alt" id="all-reviews">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('All reviews') }}</p>
          <h2 class="section__title">{{ __('What travelers say') }}</h2>
        </header>

        <form class="review-tools" data-reveal method="get" action="{{ lroute('reviews') }}#all-reviews">
          <div class="chips" id="review-filters" role="group" aria-label="{{ __('Filter reviews by destination') }}">
            <a class="chip" href="{{ lroute('reviews') }}?{{ http_build_query(['sort' => $sort]) }}#all-reviews" @if (! $active) aria-current="true" @endif>{{ t('All ({n})', ['n' => to_locale_digits($total)]) }}</a>
            @foreach ($destinations as $place)
            <a class="chip" href="{{ lroute('reviews') }}?{{ http_build_query(['destination' => $place->slug, 'sort' => $sort]) }}#all-reviews" @if ($active?->is($place)) aria-current="true" @endif>{{ $place->name }} ({{ to_locale_digits($place->reviews_count) }})</a>
            @endforeach
          </div>
          <div class="review-sort">
            <label for="review-sort">{{ __('Sort by') }}</label>
            <select id="review-sort" name="sort" data-autosubmit>
              <option value="newest" @selected($sort === 'newest')>{{ __('Newest') }}</option>
              <option value="highest" @selected($sort === 'highest')>{{ __('Highest rated') }}</option>
            </select>
            @if ($active)<input type="hidden" name="destination" value="{{ $active->slug }}">@endif
            <noscript><button type="submit" class="btn btn--outline">{{ __('Apply') }}</button></noscript>
          </div>
        </form>

        <p class="review-status" id="review-status" aria-live="polite">@if ($matching){{ t($matching === 1 ? 'Showing {a} of {b} review' : 'Showing {a} of {b} reviews', ['a' => to_locale_digits($reviews->count()), 'b' => to_locale_digits($matching)]) }}@endif</p>

        <div class="reviews-grid" id="reviews-grid">
          @forelse ($reviews as $review)
          @include('partials.cards.review', ['review' => $review, 'showType' => true])
          @empty
          <p class="reviews-empty">{{ __('No reviews for this destination yet. Be the first to write one!') }}</p>
          @endforelse
        </div>

        @if ($reviews->count() < $matching)
        <div class="reviews-more">
          <a class="btn btn--outline" id="reviews-more" href="{{ lroute('reviews') }}?{{ http_build_query(array_filter(['destination' => $active?->slug, 'sort' => $sort, 'show' => $show + $pageSize])) }}#all-reviews">{{ __('Show more reviews') }}</a>
        </div>
        @endif
      </div>
    </section>


    <!-- =====================================================
         4. WRITE A REVIEW
         ===================================================== -->
    <section class="section" id="write">
      <div class="container booking">

        <div class="booking__info" data-reveal>
          <p class="eyebrow">{{ __('Share your story') }}</p>
          <h2 class="section__title">{{ __('Travelled with us?') }}</h2>
          <p class="section__lead">{{ __('Your review helps other travelers choose with confidence. Send it to us and we will publish it after a quick check.') }}</p>
          <ul class="tick-list">
            <li>{{ __('Tell us what you loved, and what we could improve') }}</li>
            <li>{{ __('Mention the destination and who you travelled with') }}</li>
            <li>{{ __('Honest reviews only, whether good or critical') }}</li>
          </ul>
        </div>

        <form method="post" action="{{ lurl('reviews') }}" class="booking__form" id="review-form" novalidate data-reveal>
          @csrf
          <input type="hidden" name="_fragment" value="write">
          <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <div class="form-row">
            <div class="form-field{{ $errors->has('name') ? ' has-error' : '' }}">
              <label for="r-name">{{ __('Your name') }}</label>
              <input type="text" id="r-name" name="name" placeholder="{{ __('Your name') }}" required autocomplete="name" value="{{ old('name') }}">
              <small class="form-error" aria-live="polite">{{ $errors->first('name') }}</small>
            </div>
            <div class="form-field{{ $errors->has('destination') ? ' has-error' : '' }}">
              <label for="r-destination">{{ __('Destination') }}</label>
              <select id="r-destination" name="destination" required>
                <option value="" @selected(old('destination') === '')>{{ __('Select destination') }}</option>
                <option value="Cox's Bazar" @selected(old('destination') === 'Cox\'s Bazar')>{{ __('Cox\'s Bazar') }}</option>
                <option value="Sundarbans" @selected(old('destination') === 'Sundarbans')>{{ __('Sundarbans') }}</option>
                <option value="Sylhet" @selected(old('destination') === 'Sylhet')>{{ __('Sylhet') }}</option>
                <option value="Bandarban" @selected(old('destination') === 'Bandarban')>{{ __('Bandarban') }}</option>
                <option value="Saint Martin's Island" @selected(old('destination') === 'Saint Martin\'s Island')>{{ __('Saint Martin\'s Island') }}</option>
                <option value="Kuakata" @selected(old('destination') === 'Kuakata')>{{ __('Kuakata') }}</option>
              </select>
              <small class="form-error" aria-live="polite">{{ $errors->first('destination') }}</small>
            </div>
          </div>

          <div class="form-field{{ $errors->has('rating') ? ' has-error' : '' }}" id="rating-field">
            <span class="form-label" id="r-rating-label">{{ __('Your rating') }}</span>
            <div class="star-input" role="radiogroup" aria-labelledby="r-rating-label">
              <input type="radio" id="star5" name="rating" value="5" @checked(old('rating') == '5')><label for="star5" title="{{ __('5 stars') }}"><span class="sr-only">{{ __('5 stars') }}</span></label>
              <input type="radio" id="star4" name="rating" value="4" @checked(old('rating') == '4')><label for="star4" title="{{ __('4 stars') }}"><span class="sr-only">{{ __('4 stars') }}</span></label>
              <input type="radio" id="star3" name="rating" value="3" @checked(old('rating') == '3')><label for="star3" title="{{ __('3 stars') }}"><span class="sr-only">{{ __('3 stars') }}</span></label>
              <input type="radio" id="star2" name="rating" value="2" @checked(old('rating') == '2')><label for="star2" title="{{ __('2 stars') }}"><span class="sr-only">{{ __('2 stars') }}</span></label>
              <input type="radio" id="star1" name="rating" value="1" @checked(old('rating') == '1')><label for="star1" title="{{ __('1 star') }}"><span class="sr-only">{{ __('1 star') }}</span></label>
            </div>
            <small class="form-error" aria-live="polite"></small>
          </div>

          <div class="form-field{{ $errors->has('text') ? ' has-error' : '' }}">
            <label for="r-text">{{ __('Your review') }}</label>
            <textarea id="r-text" name="text" rows="5" placeholder="{{ __('What made your trip special?') }}" required minlength="20">{{ old('text') }}</textarea>
            <small class="form-error" aria-live="polite">{{ $errors->first('text') }}</small>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary btn--lg">{{ __('Send Review via WhatsApp') }}</button>
            <p class="form-note">{{ __('We may edit for length and clarity. Your phone number is never published.') }}</p>
          </div>

          <p class="form-success" id="review-success" role="status" hidden>{{ __('Thank you! Please complete sending in WhatsApp and we\'ll publish your review soon.') }}</p>
        </form>

      </div>
    </section>


    <!-- =====================================================
         5. CLOSING CTA
         ===================================================== -->
    <section class="cta-band" aria-label="{{ __('Plan your trip') }}">
      <div class="container cta-band__inner" data-reveal>
        <div>
          <h2 class="cta-band__title">{!! __('Ready to write <em>your own story?</em>') !!}</h2>
          <p>{{ __('Pick a plan and we will take care of the rest.') }}</p>
        </div>
        <a href="{{ lroute('packages') }}" class="btn btn--primary btn--lg">{{ __('View Packages') }}</a>
      </div>
    </section>

  
@endsection

@push('scripts')
  <script src="{{ asset_js('reviews.js') }}" defer></script>
@endpush

@push('structured-data')
  {!! \App\Support\StructuredData::script(\App\Support\StructuredData::breadcrumbs([[__('Home'), lroute('home')], [__('Reviews'), lroute('reviews')]])) !!}
@endpush
