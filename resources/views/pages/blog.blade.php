@extends('layouts.app', [
  'title' => 'Travel Blog: Guides, Stories & Tips',
  'description' => 'Guides, itineraries and honest tips from TravelOrio\'s local guides for exploring Cox\'s Bazar, the Sundarbans, Sylhet, Bandarban, Saint Martin\'s and Kuakata.',
  'bodyClass' => 'page-blog',
])

@section('content')
    <!-- =====================================================
         1. PAGE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="{{ __('Blog introduction') }}">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>

      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
          <ol>
            <li><a href="{{ lroute('home') }}">{{ __('Home') }}</a></li>
            <li aria-current="page">{{ __('Blog') }}</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow">{{ __('Blog') }}</p>
        <h1 class="page-hero__title">{{ __('Stories, guides') }} <em>{{ __('& tips') }}</em></h1>
        <p class="page-hero__lead">{{ __('Practical advice and honest stories from our guides, to help you plan a better trip around Bangladesh.') }}</p>
        <div class="page-hero__actions">
          <a href="#articles" class="btn btn--primary btn--lg">{{ __('Browse Articles') }}</a>
          <a href="{{ lroute('packages') }}" class="btn btn--ghost btn--lg">{{ __('Plan a Trip') }}</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. ARTICLES
         ===================================================== -->
    <section class="section" id="articles">
      <div class="container">

        <form class="blog-tools" data-reveal method="get" action="{{ lroute('blog') }}#articles" role="search">
          <div class="chips" id="blog-filters" role="group" aria-label="{{ __('Filter articles by category') }}">
            <a class="chip" href="{{ lroute('blog') }}{{ $search !== '' ? '?'.http_build_query(['q' => $search]) : '' }}#articles" @if (! $category) aria-current="true" @endif>{{ __('All') }} ({{ to_locale_digits($allCount) }})</a>
            @foreach ($categories as $item)
            <a class="chip" href="{{ lroute('blog') }}?{{ http_build_query(array_filter(['category' => $item->slug, 'q' => $search])) }}#articles" @if ($category?->is($item)) aria-current="true" @endif>{{ $item->name }} ({{ to_locale_digits($item->posts_count) }})</a>
            @endforeach
          </div>
          <div class="blog-search">
            <label for="blog-search" class="sr-only">{{ __('Search articles') }}</label>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            <input type="search" id="blog-search" name="q" value="{{ $search }}" placeholder="{{ __('Search articles…') }}" autocomplete="off">
            @if ($category)<input type="hidden" name="category" value="{{ $category->slug }}">@endif
          </div>
        </form>
        <p class="review-status" id="blog-status" aria-live="polite">@if ($total){{ t($total === 1 ? '{n} article' : '{n} articles', ['n' => to_locale_digits($total)]) }}@endif</p>

        @if ($featured)
        <div id="blog-featured" class="blog-featured">
          @include('partials.cards.post', ['post' => $featured, 'isFeatured' => true])
        </div>
        @endif
        <div class="grid grid--3" id="blog-grid">
          @forelse ($posts as $post)
          @include('partials.cards.post', ['post' => $post])
          @empty
          @unless ($featured)
          <p class="reviews-empty">{{ __('No articles match your search.') }}</p>
          @endunless
          @endforelse
        </div>

        @if ($hasMore)
        <div class="reviews-more">
          <a class="btn btn--outline" id="blog-more" href="{{ lroute('blog') }}?{{ http_build_query(array_filter(['category' => $category?->slug, 'q' => $search, 'show' => $show + $pageSize])) }}#articles">{{ __('Show more articles') }}</a>
        </div>
        @endif
      </div>
    </section>


    <!-- =====================================================
         3. CLOSING CTA
         ===================================================== -->
    <section class="cta-band" aria-label="{{ __('Plan your trip') }}">
      <div class="container cta-band__inner" data-reveal>
        <div>
          <h2 class="cta-band__title">{!! __('Ready to turn <em>reading into travelling?</em>') !!}</h2>
          <p>{{ __('Tell us your dates and we will plan it for you.') }}</p>
        </div>
        <a href="{{ lroute('packages') }}" class="btn btn--primary btn--lg">{{ __('View Packages') }}</a>
      </div>
    </section>

  
@endsection

@push('structured-data')
  {!! \App\Support\StructuredData::script(\App\Support\StructuredData::breadcrumbs([[__('Home'), lroute('home')], [__('Blog'), lroute('blog')]])) !!}
@endpush
