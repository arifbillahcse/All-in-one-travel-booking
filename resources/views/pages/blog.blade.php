@extends('layouts.app', [
  'title' => 'Travel Blog: Guides, Stories & Tips',
  'description' => 'Guides, itineraries and honest tips from TravelOrio\'s local guides for exploring Cox\'s Bazar, the Sundarbans, Sylhet, Bandarban, Saint Martin\'s and Kuakata.',
  'bodyClass' => 'page-blog',
])

@section('content')
    <!-- =====================================================
         1. PAGE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="Blog introduction">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>

      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <ol>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li aria-current="page">Blog</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow">Blog</p>
        <h1 class="page-hero__title">Stories, guides <em>&amp; tips</em></h1>
        <p class="page-hero__lead">Practical advice and honest stories from our guides, to help you plan a better trip around Bangladesh.</p>
        <div class="page-hero__actions">
          <a href="#articles" class="btn btn--primary btn--lg">Browse Articles</a>
          <a href="{{ route('packages') }}" class="btn btn--ghost btn--lg">Plan a Trip</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. ARTICLES (rendered from js/blog-core.js)
         ===================================================== -->
    <section class="section" id="articles">
      <div class="container">

        <div class="blog-tools" data-reveal>
          <div class="chips" id="blog-filters" role="group" aria-label="Filter articles by category"></div>
          <div class="blog-search">
            <label for="blog-search" class="sr-only">Search articles</label>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            <input type="search" id="blog-search" placeholder="Search articles…" autocomplete="off">
          </div>
        </div>
        <p class="review-status" id="blog-status" aria-live="polite"></p>

        <div id="blog-featured" class="blog-featured" hidden></div>
        <div class="grid grid--3" id="blog-grid"></div>

        <div class="reviews-more">
          <button type="button" class="btn btn--outline" id="blog-more" hidden>Show more articles</button>
        </div>
      </div>
    </section>


    <!-- =====================================================
         3. CLOSING CTA
         ===================================================== -->
    <section class="cta-band" aria-label="Plan your trip">
      <div class="container cta-band__inner" data-reveal>
        <div>
          <h2 class="cta-band__title" data-i18n-html>Ready to turn <em>reading into travelling?</em></h2>
          <p>Tell us your dates and we will plan it for you.</p>
        </div>
        <a href="{{ route('packages') }}" class="btn btn--primary btn--lg">View Packages</a>
      </div>
    </section>

  
@endsection

@push('scripts-data')
  <script src="{{ asset_js('blog-core.js') }}" defer></script>
@endpush

@push('scripts-i18n')
  <script src="{{ asset_js('i18n/blog-bn.js') }}" defer></script>
@endpush

@push('scripts')
  <script src="{{ asset_js('blog.js') }}" defer></script>
@endpush
