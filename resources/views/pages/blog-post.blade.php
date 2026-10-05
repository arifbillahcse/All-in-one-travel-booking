@extends('layouts.app', [
  'title' => 'Article',
  'description' => 'Travel guides and tips from TravelOrio.',
  'bodyClass' => 'page-blog page-article',
])

@section('content')
    <div class="read-progress" id="read-progress" aria-hidden="true"></div>

    <!-- =====================================================
         1. ARTICLE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="Article introduction">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>

      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <ol>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('blog') }}">Blog</a></li>
            <li aria-current="page" id="post-crumb" data-no-i18n>Article</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow" id="post-cat" data-no-i18n>Guides</p>
        <h1 class="page-hero__title page-hero__title--article" id="post-title" data-no-i18n>Travel story</h1>
        <p class="page-hero__lead" id="post-meta" data-no-i18n></p>
      </div>
    </section>


    <!-- =====================================================
         2. ARTICLE
         ===================================================== -->
    <section class="section" id="story">
      <div class="container container--article">
        <article class="article" id="article" data-no-i18n></article>
      </div>
    </section>


    <!-- =====================================================
         3. PREVIOUS / NEXT + RELATED
         ===================================================== -->
    <section class="section section--alt" id="more-reading">
      <div class="container">
        <nav class="post-nav" id="post-nav" aria-label="More articles" data-no-i18n></nav>

        <header class="section__header" data-reveal>
          <p class="eyebrow">Keep reading</p>
          <h2 class="section__title">Related articles</h2>
        </header>
        <div class="grid grid--3" id="related-posts"></div>
      </div>
    </section>


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
  <script>window.TRAVELORIO_PAGE = { slug: @json($slug) };</script>
@endpush

@push('scripts-data')
  <script src="{{ asset_js('blog-core.js') }}" defer></script>
  <script src="{{ asset_js('data.js') }}" defer></script>
@endpush

@push('scripts-i18n')
  <script src="{{ asset_js('i18n/data-bn.js') }}" defer></script>
  <script src="{{ asset_js('i18n/blog-bn.js') }}" defer></script>
@endpush

@push('scripts')
  <script src="{{ asset_js('blog-post.js') }}" defer></script>
@endpush
