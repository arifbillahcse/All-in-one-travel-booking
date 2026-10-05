@extends('layouts.app', [
  'title' => 'Tour Packages & Pricing',
  'description' => 'Compare TravelOrio tour packages for Bangladesh: Weekend Escape, Explorer and Grand Bangladesh. Transparent per-person pricing, add-ons and instant WhatsApp booking.',
  'bodyClass' => 'page-packages',
])

@section('content')
    <!-- =====================================================
         1. PAGE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="Packages introduction">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>

      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <ol>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li aria-current="page">Packages</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow">Packages &amp; Pricing</p>
        <h1 class="page-hero__title">Pick a plan. <em>We'll plan the rest.</em></h1>
        <p class="page-hero__lead">Three clear options for exploring Bangladesh, priced per person with no hidden fees. Mix and match any of our six destinations.</p>
        <div class="page-hero__actions">
          <a href="#plans" class="btn btn--primary btn--lg">See the Plans</a>
          <a href="#compare" class="btn btn--ghost btn--lg">Compare Features</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. PLANS
         ===================================================== -->
    <section class="section" id="plans">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">Our plans</p>
          <h2 class="section__title">Choose how you want to travel</h2>
          <p class="section__lead">Prices are per person for twin-sharing. Your final quote depends on the destination, season and hotel category.</p>
        </header>

        <div class="grid grid--3 packages">

          <article class="package" data-reveal>
            <h3 class="package__name">Weekend Escape</h3>
            <p class="package__desc">A quick, restful getaway.</p>
            <p class="package__best">Best for couples and short breaks</p>
            <p class="package__price"><span class="package__currency">৳</span>5,500<small> / person</small></p>
            <ul class="package__list">
              <li>2 days, 1 night</li>
              <li>Standard hotel stay</li>
              <li>Breakfast included</li>
              <li>Local transport</li>
              <li>1 destination</li>
            </ul>
            <a href="#booking" class="btn btn--outline btn--block" data-package="Weekend Escape">Choose Weekend Escape</a>
          </article>

          <article class="package package--featured" data-reveal>
            <span class="package__badge">Most Popular</span>
            <h3 class="package__name">Explorer</h3>
            <p class="package__desc">Our complete experience.</p>
            <p class="package__best">Best for families and friends</p>
            <p class="package__price"><span class="package__currency">৳</span>12,500<small> / person</small></p>
            <ul class="package__list">
              <li>4 days, 3 nights</li>
              <li>Premium hotel or resort</li>
              <li>All meals included</li>
              <li>Private transport and guide</li>
              <li>Entry tickets and activities</li>
            </ul>
            <a href="#booking" class="btn btn--primary btn--block" data-package="Explorer">Choose Explorer</a>
          </article>

          <article class="package" data-reveal>
            <h3 class="package__name">Grand Bangladesh</h3>
            <p class="package__desc">Multiple destinations in one trip.</p>
            <p class="package__best">Best for first-time visitors</p>
            <p class="package__price"><span class="package__currency">৳</span>28,000<small> / person</small></p>
            <ul class="package__list">
              <li>8 days, 7 nights</li>
              <li>3 destinations of your choice</li>
              <li>Luxury stays</li>
              <li>Dedicated trip manager</li>
              <li>Airport pickup and drop</li>
            </ul>
            <a href="#booking" class="btn btn--outline btn--block" data-package="Grand Bangladesh">Choose Grand Bangladesh</a>
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
          <p class="eyebrow">Compare</p>
          <h2 class="section__title">What's in each plan</h2>
        </header>

        <p class="compare-hint" aria-hidden="true">Swipe sideways to compare plans →</p>
        <div class="compare-wrap" data-reveal>
          <table class="compare">
            <caption class="sr-only">Feature comparison of TravelOrio packages</caption>
            <thead>
              <tr>
                <th scope="col"><span class="sr-only">Feature</span></th>
                <th scope="col">Weekend Escape</th>
                <th scope="col" class="compare__featured">Explorer</th>
                <th scope="col">Grand Bangladesh</th>
              </tr>
            </thead>
            <tbody>
              <tr><th scope="row">Price per person</th><td>৳5,500</td><td class="compare__featured">৳12,500</td><td>৳28,000</td></tr>
              <tr><th scope="row">Duration</th><td>2 days</td><td class="compare__featured">4 days</td><td>8 days</td></tr>
              <tr><th scope="row">Destinations</th><td>1</td><td class="compare__featured">1</td><td>3</td></tr>
              <tr><th scope="row">Hotel</th><td>Standard</td><td class="compare__featured">Premium</td><td>Luxury</td></tr>
              <tr><th scope="row">Meals</th><td>Breakfast</td><td class="compare__featured">All meals</td><td>All meals</td></tr>
              <tr><th scope="row">Transport</th><td>Local</td><td class="compare__featured">Private AC vehicle</td><td>Private AC vehicle</td></tr>
              <tr><th scope="row">Local guide</th><td><span class="no" aria-label="Not included">—</span></td><td class="compare__featured"><span class="yes" aria-label="Included">✓</span></td><td><span class="yes" aria-label="Included">✓</span></td></tr>
              <tr><th scope="row">Entry tickets and activities</th><td><span class="no" aria-label="Not included">—</span></td><td class="compare__featured"><span class="yes" aria-label="Included">✓</span></td><td><span class="yes" aria-label="Included">✓</span></td></tr>
              <tr><th scope="row">Airport pickup and drop</th><td><span class="no" aria-label="Not included">—</span></td><td class="compare__featured"><span class="no" aria-label="Not included">—</span></td><td><span class="yes" aria-label="Included">✓</span></td></tr>
              <tr><th scope="row">Dedicated trip manager</th><td><span class="no" aria-label="Not included">—</span></td><td class="compare__featured"><span class="no" aria-label="Not included">—</span></td><td><span class="yes" aria-label="Included">✓</span></td></tr>
              <tr><th scope="row">Free cancellation</th><td>7 days before</td><td class="compare__featured">7 days before</td><td>7 days before</td></tr>
            </tbody>
          </table>
        </div>
        <p class="compare-note" data-reveal>Need something different? Every plan can be customised. Tell us what you'd like to change in the request form below.</p>
      </div>
    </section>


    <!-- =====================================================
         4. TRIPS BY DESTINATION (rendered from js/data.js)
         ===================================================== -->
    <section class="section" id="by-destination">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">By destination</p>
          <h2 class="section__title">Or start with a place</h2>
          <p class="section__lead">Each trip has its own itinerary and starting price. Pick one to see the full plan.</p>
        </header>

        <div class="grid grid--3" id="destination-cards">
          <noscript>
            <p>Browse destinations on the <a href="{{ route('home') }}#destinations">home page</a>.</p>
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
          <p class="eyebrow">Optional extras</p>
          <h2 class="section__title">Make it your own</h2>
          <p class="section__lead">Add any of these to a plan. Mention them in your request and we'll include them in the quote.</p>
        </header>

        <div class="grid grid--4 addons">

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2L16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.300.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            </div>
            <h3 class="addon__title">Airport transfer</h3>
            <p class="addon__text">Private pickup and drop in an AC car.</p>
            <p class="addon__price">from ৳1,500</p>
          </div>

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 1 2-2h2l1.5-2h7L17 6h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><circle cx="12" cy="13" r="3.5"/></svg>
            </div>
            <h3 class="addon__title">Trip photographer</h3>
            <p class="addon__text">A professional to capture your best moments.</p>
            <p class="addon__price">from ৳4,000 / day</p>
          </div>

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l2 3h14l2-3z"/><path d="M12 3v11M12 4l6 8h-6M12 6L7 12h5"/></svg>
            </div>
            <h3 class="addon__title">Private boat</h3>
            <p class="addon__text">Skip the crowds with your own boat and crew.</p>
            <p class="addon__price">from ৳3,500</p>
          </div>

          <div class="addon" data-reveal>
            <div class="addon__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20V8M3 14h18v6M21 14v-2a3 3 0 0 0-3-3h-7v5"/><circle cx="7" cy="11" r="1.5"/></svg>
            </div>
            <h3 class="addon__title">Extra night</h3>
            <p class="addon__text">Stay longer at the same hotel and rate.</p>
            <p class="addon__price">from ৳2,800 / room</p>
          </div>

        </div>
      </div>
    </section>


    <!-- =====================================================
         6. GROUP / CUSTOM TRIP BANNER
         ===================================================== -->
    <section class="cta-band" aria-label="Group and custom trips">
      <div class="container cta-band__inner" data-reveal>
        <div>
          <h2 class="cta-band__title" data-i18n-html>Travelling with <em>6 or more?</em></h2>
          <p>Groups, families and corporate tours get a reduced per-person price and a dedicated trip manager.</p>
        </div>
        <a href="{{ whatsapp_url() }}?text=Hello%20TravelOrio!%20I'd%20like%20a%20group%20quote." class="btn btn--primary btn--lg" target="_blank" rel="noopener">Get a Group Quote</a>
      </div>
    </section>


    <!-- =====================================================
         7. FAQ
         ===================================================== -->
    <section class="section" id="faq">
      <div class="container container--narrow">
        <header class="section__header" data-reveal>
          <p class="eyebrow">Pricing FAQ</p>
          <h2 class="section__title">Questions about packages</h2>
        </header>

        <div class="faq" data-reveal>
          <details class="faq__item">
            <summary>What does "per person" mean?</summary>
            <p>Prices are for each traveler based on two people sharing a room. Single travelers pay a small single-supplement, which we show in your quote.</p>
          </details>
          <details class="faq__item">
            <summary>Why do prices vary by destination?</summary>
            <p>Hotels, transport and entry fees cost different amounts in each region. The plan prices above are typical starting points, and the exact figure appears in your personalised quote.</p>
          </details>
          <details class="faq__item">
            <summary>How do I pay?</summary>
            <p>No payment is taken online. After you send a request we confirm availability and share bKash, Nagad or bank transfer details. A deposit holds your booking and the balance is due before departure.</p>
          </details>
          <details class="faq__item">
            <summary>Can I change my plan after booking?</summary>
            <p>Yes, as long as hotels and transport allow it. Tell us as early as you can and we will rebook without extra fees wherever possible.</p>
          </details>
          <details class="faq__item">
            <summary>What is your cancellation policy?</summary>
            <p>Free cancellation up to 7 days before departure. After that, charges depend on hotel and transport bookings already made.</p>
          </details>
          <details class="faq__item">
            <summary>Are flights and bus tickets included?</summary>
            <p>Long-distance tickets are not included in the plan prices, but we can book them for you at the actual cost.</p>
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
          <p class="eyebrow">Request a quote</p>
          <h2 class="section__title">Tell us your plan</h2>
          <p class="section__lead">Choose a plan and destination, add your dates, and we'll reply with a personalised quote within a few hours.</p>

          <div class="estimate" aria-live="polite">
            <p class="estimate__label">Estimated total</p>
            <p class="estimate__total" id="estimate-total">Choose a plan</p>
            <p class="estimate__note" id="estimate-note">A rough guide based on the plan price. Your final quote may differ by destination and season.</p>
          </div>

          <ul class="contact-list" id="contact">
            <li>
              <span class="contact-list__label">WhatsApp</span>
              <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener">{{ site('phone_display') }}</a>
            </li>
            <li>
              <span class="contact-list__label">Email</span>
              <a href="mailto:{{ site('email') }}">{{ site('email') }}</a>
            </li>
            <li>
              <span class="contact-list__label">Hours</span>
              <span>Sat–Thu, 9:00 AM – 9:00 PM</span>
            </li>
          </ul>
        </div>

        <form class="booking__form" id="booking-form" novalidate data-reveal>
          <input type="hidden" name="estimate" id="b-estimate" value="">

          <div class="form-row">
            <div class="form-field">
              <label for="b-name">Full name</label>
              <input type="text" id="b-name" name="name" placeholder="Your name" required autocomplete="name">
              <small class="form-error" aria-live="polite"></small>
            </div>
            <div class="form-field">
              <label for="b-phone">Phone / WhatsApp</label>
              <input type="tel" id="b-phone" name="phone" placeholder="+880 1XXX-XXXXXX" required autocomplete="tel">
              <small class="form-error" aria-live="polite"></small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label for="b-package">Plan</label>
              <select id="b-package" name="package">
                <option value="" data-price="">Not sure yet</option>
                <option value="Weekend Escape" data-price="5500">Weekend Escape · ৳5,500</option>
                <option value="Explorer" data-price="12500">Explorer · ৳12,500</option>
                <option value="Grand Bangladesh" data-price="28000">Grand Bangladesh · ৳28,000</option>
              </select>
              <small class="form-error" aria-hidden="true"></small>
            </div>
            <div class="form-field">
              <label for="b-destination">Destination</label>
              <select id="b-destination" name="destination" required>
                <option value="">Select destination</option>
                <option value="Cox's Bazar">Cox's Bazar</option>
                <option value="Sundarbans">Sundarbans</option>
                <option value="Sylhet">Sylhet</option>
                <option value="Bandarban">Bandarban</option>
                <option value="Saint Martin's Island">Saint Martin's Island</option>
                <option value="Kuakata">Kuakata</option>
              </select>
              <small class="form-error" aria-live="polite"></small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label for="b-date">Travel date</label>
              <input type="date" id="b-date" name="date" required>
              <small class="form-error" aria-live="polite"></small>
            </div>
            <div class="form-field">
              <label for="b-guests">Number of travelers</label>
              <input type="number" id="b-guests" name="guests" min="1" max="50" value="2" required>
              <small class="form-error" aria-live="polite"></small>
            </div>
          </div>

          <div class="form-field">
            <label for="b-message">Message <span class="optional">(optional)</span></label>
            <textarea id="b-message" name="message" rows="4" placeholder="Add-ons, dietary needs, special occasion, budget…"></textarea>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary btn--lg">Send via WhatsApp</button>
            <p class="form-note">We never share your details. No payment is taken at this step.</p>
          </div>

          <p class="form-success" id="form-success" role="status" hidden>Thank you! Your request is ready. Complete it in WhatsApp and we'll reply shortly.</p>
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
