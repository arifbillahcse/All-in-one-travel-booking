<footer class="footer">
    <div class="container footer__grid">

      <div class="footer__brand">
        <a href="{{ route('home') }}" class="navbar__logo footer__logo">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="9"/>
            <path d="M15.5 8.5l-2 5-5 2 2-5 5-2z"/>
          </svg>
          <span>Travel<em class="logo-accent">Orio</em></span>
        </a>
        <p>Curated tours to the most beautiful places in Bangladesh.</p>
      </div>

      <nav class="footer__col" aria-label="Destinations">
        <h4>Destinations</h4>
        <ul>
          @foreach (site('destinations') as $slug => $name)
          <li><a href="{{ route('destination', $slug) }}">{{ $name }}</a></li>
          @endforeach
        </ul>
      </nav>

      <nav class="footer__col" aria-label="Company">
        <h4>Company</h4>
        <ul>
          <li><a href="{{ route('packages') }}">Packages</a></li>
          <li><a href="{{ route('why-us') }}">Why Us</a></li>
          <li><a href="{{ route('reviews') }}">Reviews</a></li>
          <li><a href="{{ route('blog') }}">Blog</a></li>
          <li><a href="{{ route('contact') }}">Contact</a></li>
        </ul>
      </nav>

      <div class="footer__col">
        <h4>Stay in touch</h4>
        <ul>
          <li><a href="mailto:{{ site('email') }}">{{ site('email') }}</a></li>
          <li><a href="{{ whatsapp_url() }}" target="_blank" rel="noopener">WhatsApp us</a></li>
        </ul>
        <div class="footer__social">
          <a href="{{ site('social.facebook') }}" aria-label="Facebook">Facebook</a>
          <a href="{{ site('social.instagram') }}" aria-label="Instagram">Instagram</a>
          <a href="{{ site('social.youtube') }}" aria-label="YouTube">YouTube</a>
        </div>
      </div>

    </div>

    <div class="footer__bottom">
      <div class="container">
        <p>&copy; <span id="year">{{ date('Y') }}</span> TravelOrio. All rights reserved.</p>
      </div>
    </div>
  </footer>
