@extends('layouts.app', [
  'bodyClass' => 'page-blog page-article',
])

@section('content')
    <div class="read-progress" id="read-progress" aria-hidden="true"></div>

    <!-- =====================================================
         1. ARTICLE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="{{ __('Article introduction') }}">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>

      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
          <ol>
            <li><a href="{{ lroute('home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ lroute('blog') }}">{{ __('Blog') }}</a></li>
            <li aria-current="page" id="post-crumb">{{ $post->title }}</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow" id="post-cat">{{ $post->category->name }}</p>
        <h1 class="page-hero__title page-hero__title--article" id="post-title">{{ $post->title }}</h1>
        <p class="page-hero__lead" id="post-meta">{{ format_date($post->published_at) }} · {{ t('{n} min read', ['n' => to_locale_digits($post->read_minutes)]) }}</p>
      </div>
    </section>


    <!-- =====================================================
         2. ARTICLE
         ===================================================== -->
    <section class="section" id="story">
      <div class="container container--article">
        <article class="article" id="article">
          @php($blocks = collect($post->body))
          @php($headings = $blocks->where('type', 'h2')->values())
          <figure class="article__cover"><img src="{{ placeholder_image('blog-'.$post->slug, 1200, 700) }}" alt="{{ $post->title }}" width="1200" height="700"></figure>

          @if ($headings->count() > 1)
          <nav class="toc" aria-label="{{ __('In this article') }}"><p class="toc__title">{{ __('In this article') }}</p><ol>
            @foreach ($headings as $heading)
            <li><a href="#sec-{{ $loop->iteration }}">{{ $heading['text'] }}</a></li>
            @endforeach
          </ol></nav>
          @endif

          <div class="article__body">
            @php($section = 0)
            @foreach ($blocks as $block)
              @switch($block['type'])
                @case('h2')
                  <h2 id="sec-{{ ++$section }}">{{ $block['text'] }}</h2>
                  @break
                @case('ul')
                  <ul class="tick-list">@foreach ($block['items'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                  @break
                @case('tip')
                  <aside class="callout"><strong>{{ __('Tip') }}</strong><p>{{ $block['text'] }}</p></aside>
                  @break
                @default
                  <p>{{ $block['text'] }}</p>
              @endswitch
            @endforeach
          </div>

          @if ($post->destination)
          <aside class="trip-card">
            <div><p class="eyebrow">{{ __('Plan this trip') }}</p><h3>{{ $post->destination->name }}</h3><p>{{ $post->destination->tagline }}</p></div>
            <div class="trip-card__actions">
              <a class="btn btn--primary" href="{{ lroute('destination', $post->destination->slug) }}">{{ __('View trip details') }}</a>
              <a class="btn btn--outline" href="{{ lroute('packages') }}?place={{ $post->destination->slug }}">{{ __('See packages') }}</a>
            </div>
          </aside>
          @endif

          <div class="share"><span class="share__label">{{ __('Share this article') }}</span>
            <a class="share__btn" target="_blank" rel="noopener" href="https://wa.me/?text={{ rawurlencode($post->title.' '.url()->current()) }}">WhatsApp</a>
            <a class="share__btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}">Facebook</a>
            <button type="button" class="share__btn" id="copy-link">{{ __('Copy link') }}</button></div>

          <aside class="author-box"><span class="author-box__avatar" aria-hidden="true">T</span><div><p class="author-box__name">{{ __('TravelOrio Team') }}</p><p>{{ __('Local guides and trip planners who write from first-hand experience.') }}</p></div></aside>
        </article>
      </div>
    </section>


    <!-- =====================================================
         3. PREVIOUS / NEXT + RELATED
         ===================================================== -->
    <section class="section section--alt" id="more-reading">
      <div class="container">
        <nav class="post-nav" id="post-nav" aria-label="{{ __('More articles') }}">
          @if ($previous)
          <a class="post-nav__link" href="{{ lroute('blog.post', $previous->slug) }}"><span>{{ __('Previous article') }}</span><strong>{{ $previous->title }}</strong></a>
          @else<span></span>@endif
          @if ($next)
          <a class="post-nav__link post-nav__link--next" href="{{ lroute('blog.post', $next->slug) }}"><span>{{ __('Next article') }}</span><strong>{{ $next->title }}</strong></a>
          @else<span></span>@endif
        </nav>

        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Keep reading') }}</p>
          <h2 class="section__title">{{ __('Related articles') }}</h2>
        </header>
        <div class="grid grid--3" id="related-posts">
          @foreach ($related as $item)
          @include('partials.cards.post', ['post' => $item])
          @endforeach
        </div>
      </div>
    </section>


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

@push('scripts')
  <script src="{{ asset_js('blog-post.js') }}" defer></script>
@endpush
