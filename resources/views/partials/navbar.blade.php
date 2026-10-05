<header class="navbar" id="navbar">
    <div class="container navbar__inner">
      <a href="{{ route('home') }}" class="navbar__logo" aria-label="TravelOrio home">
        <svg class="navbar__logo-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="9"/>
          <path d="M15.5 8.5l-2 5-5 2 2-5 5-2z"/>
        </svg>
        <span>Travel<em class="logo-accent">Orio</em></span>
      </a>

      <nav class="navbar__nav" id="nav-menu" aria-label="Main navigation">
        <ul class="navbar__links">
          <li class="has-menu">
            <a href="{{ route('home') }}#destinations" @if(request()->routeIs('destination')) aria-current="page" @endif>Destinations</a>
            <button type="button" class="submenu-toggle" aria-expanded="false" aria-controls="submenu-destinations" aria-label="Show destinations"><svg viewBox="0 0 12 8" aria-hidden="true"><path d="M1 1.5l5 5 5-5"/></svg></button>
            <ul class="submenu" id="submenu-destinations">
              @foreach (site('destinations') as $slug => $name)
              <li><a href="{{ route('destination', $slug) }}" @if(request()->is('destinations/'.$slug)) aria-current="page" @endif>{{ $name }}</a></li>
              @endforeach
              <li class="submenu__all"><a href="{{ route('home') }}#destinations">All destinations <span aria-hidden="true">→</span></a></li>
            </ul>
          </li>
          <li><a href="{{ route('packages') }}" @if(request()->routeIs('packages')) aria-current="page" @endif>Packages</a></li>
          <li><a href="{{ route('why-us') }}" @if(request()->routeIs('why-us')) aria-current="page" @endif>Why Us</a></li>
          <li><a href="{{ route('reviews') }}" @if(request()->routeIs('reviews')) aria-current="page" @endif>Reviews</a></li>
          <li><a href="{{ route('blog') }}" @if(request()->routeIs('blog*')) aria-current="page" @endif>Blog</a></li>
          <li><a href="{{ route('contact') }}" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a></li>
        </ul>
        <a href="{{ route('packages') }}#booking" class="btn btn--primary navbar__cta">Book Now</a>
      </nav>

      <div class="lang-switch" role="group" aria-label="Language">
        <button type="button" data-lang="en" aria-pressed="true" aria-label="Switch to English">EN</button>
        <button type="button" data-lang="bn" aria-pressed="false" aria-label="বাংলায় দেখুন" lang="bn">বাংলা</button>
      </div>

      <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Switch to dark mode" aria-pressed="false" data-no-i18n>
        <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5z"/></svg>
        <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
      </button>

      <button class="navbar__toggle" id="nav-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="nav-menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>
