@extends('layouts.app', [
  'title' => 'Contact Us',
  'description' => 'Contact TravelOrio by WhatsApp, phone, email or visit our Dhaka office. Free quotes for trips across Bangladesh.',
  'bodyClass' => 'page-contact',
])

@section('content')
    <!-- =====================================================
         1. PAGE HERO
         ===================================================== -->
    <section class="page-hero" id="home" aria-label="{{ __('Contact introduction') }}">
      <div class="page-hero__waves" aria-hidden="true">
        <svg class="wave wave--1" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,60 C240,120 480,0 720,60 C960,120 1200,0 1440,60 C1680,120 1920,0 2160,60 C2400,120 2640,0 2880,60 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--2" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,70 C240,10 480,110 720,70 C960,30 1200,110 1440,70 C1680,10 1920,110 2160,70 C2400,30 2640,110 2880,70 L2880,120 L0,120 Z"/></svg>
        <svg class="wave wave--3" viewBox="0 0 2880 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0,80 C240,120 480,40 720,80 C960,120 1200,40 1440,80 C1680,120 1920,40 2160,80 C2400,120 2640,40 2880,80 L2880,120 L0,120 Z"/></svg>
      </div>
      <div class="container page-hero__content">
        <nav class="breadcrumb" aria-label="{{ __('Breadcrumb') }}">
          <ol>
            <li><a href="{{ lroute('home') }}">{{ __('Home') }}</a></li>
            <li aria-current="page">{{ __('Contact') }}</li>
          </ol>
        </nav>
        <p class="eyebrow page-hero__eyebrow">{{ __('Contact us') }}</p>
        <h1 class="page-hero__title">{{ __('Let\'s plan') }} <em>{{ __('your trip.') }}</em></h1>
        <p class="page-hero__lead">{{ __('Message, call or visit. A real person from our team replies, usually within a few hours.') }}</p>
        <div class="page-hero__actions">
          <a href="{{ whatsapp_url() }}" class="btn btn--primary btn--lg" target="_blank" rel="noopener">{{ __('Chat on WhatsApp') }}</a>
          <a href="#message" class="btn btn--ghost btn--lg">{{ __('Send a Message') }}</a>
        </div>
      </div>
    </section>


    <!-- =====================================================
         2. CONTACT METHODS
         ===================================================== -->
    <section class="section section--tight" id="methods">
      <div class="container">
        <div class="grid grid--4 contact-cards">

          <a class="contact-card contact-card--primary" href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-reveal>
            <span class="contact-card__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.8 7L3 21l2-5.2A8 8 0 1 1 21 12z"/><path d="M9 10h6M9 14h4"/></svg>
            </span>
            <h2 class="contact-card__title">{{ __('WhatsApp') }}</h2>
            <p class="contact-card__text">{{ __('Fastest way to reach us.') }}</p>
            <p class="contact-card__value">{{ site('phone_display') }}</p>
            <span class="contact-card__tag">{{ __('Replies in ~1 hour') }}</span>
          </a>

          <a class="contact-card" href="tel:{{ site('phone') }}" data-reveal>
            <span class="contact-card__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.500 1.500a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg>
            </span>
            <h2 class="contact-card__title">{{ __('Call us') }}</h2>
            <p class="contact-card__text">{{ __('Talk to a trip planner.') }}</p>
            <p class="contact-card__value">{{ site('phone_display') }}</p>
            <span class="contact-card__tag">{{ __('Sat–Thu, 9 AM – 9 PM') }}</span>
          </a>

          <a class="contact-card" href="mailto:{{ site('email') }}" data-reveal>
            <span class="contact-card__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            </span>
            <h2 class="contact-card__title">{{ __('Email') }}</h2>
            <p class="contact-card__text">{{ __('For quotes and documents.') }}</p>
            <p class="contact-card__value">{{ site('email') }}</p>
            <span class="contact-card__tag">{{ __('Replies within a day') }}</span>
          </a>

          <a class="contact-card" href="#visit" data-reveal>
            <span class="contact-card__icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
            </span>
            <h2 class="contact-card__title">{{ __('Visit') }}</h2>
            <p class="contact-card__text">{{ __('Meet us over a cup of tea.') }}</p>
            <p class="contact-card__value">{{ __('Dhanmondi, Dhaka') }}</p>
            <span class="contact-card__tag">{{ __('By appointment') }}</span>
          </a>

        </div>
      </div>
    </section>


    <!-- =====================================================
         3. MESSAGE FORM + DETAILS
         ===================================================== -->
    <section class="section section--alt" id="message">
      <div class="container contact-split">

        <form class="booking__form" id="contact-form" novalidate data-reveal>
          <h2 class="contact-form__title">{{ __('Send us a message') }}</h2>
          <p class="contact-form__lead">{{ __('Tell us how we can help and we\'ll get back to you.') }}</p>

          <div class="form-row">
            <div class="form-field">
              <label for="c-name">{{ __('Full name') }}</label>
              <input type="text" id="c-name" name="name" placeholder="{{ __('Your name') }}" required autocomplete="name">
              <small class="form-error" aria-live="polite"></small>
            </div>
            <div class="form-field">
              <label for="c-phone">{{ __('Phone / WhatsApp') }}</label>
              <input type="tel" id="c-phone" name="phone" placeholder="{{ __('+880 1XXX-XXXXXX') }}" required autocomplete="tel">
              <small class="form-error" aria-live="polite"></small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label for="c-email">{{ __('Email') }} <span class="optional">{{ __('(optional)') }}</span></label>
              <input type="email" id="c-email" name="email" placeholder="{{ __('you@example.com') }}" autocomplete="email">
              <small class="form-error" aria-live="polite"></small>
            </div>
            <div class="form-field">
              <label for="c-topic">{{ __('Topic') }}</label>
              <select id="c-topic" name="topic">
                <option value="Planning a trip">{{ __('Planning a trip') }}</option>
                <option value="Group or corporate tour">{{ __('Group or corporate tour') }}</option>
                <option value="Existing booking">{{ __('Existing booking') }}</option>
                <option value="Partnership">{{ __('Partnership') }}</option>
                <option value="Something else">{{ __('Something else') }}</option>
              </select>
              <small class="form-error" aria-hidden="true"></small>
            </div>
          </div>

          <div class="form-field">
            <label for="c-message">{{ __('Message') }}</label>
            <textarea id="c-message" name="message" rows="6" placeholder="{{ __('Tell us where you\'d like to go, when, and with whom…') }}" required minlength="10"></textarea>
            <small class="form-error" aria-live="polite"></small>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn--primary btn--lg">{{ __('Send via WhatsApp') }}</button>
            <p class="form-note">{{ __('We never share your details. No payment is taken at this step.') }}</p>
          </div>

          <p class="form-success" id="contact-success" role="status" hidden>{{ __('Thank you! Please complete sending in WhatsApp and we\'ll reply shortly.') }}</p>
        </form>

        <aside class="contact-side" aria-label="{{ __('Contact details') }}" data-reveal>

          <div class="side-card">
            <h3>{{ __('Opening hours') }}</h3>
            <table class="hours">
              <tbody>
                <tr><th scope="row">{{ __('Saturday – Thursday') }}</th><td>{{ __('9:00 AM – 9:00 PM') }}</td></tr>
                <tr><th scope="row">{{ __('Friday') }}</th><td>{{ __('Closed (WhatsApp replies until 6 PM)') }}</td></tr>
              </tbody>
            </table>
            <p class="side-card__note">{{ __('Messages sent outside these hours are answered the next morning.') }}</p>
          </div>

          <div class="side-card">
            <h3>{{ __('Already booked?') }}</h3>
            <p>{{ __('Send your name and travel date on WhatsApp and we\'ll share your itinerary, vouchers and driver details.') }}</p>
            <a href="{{ whatsapp_url() }}?text=Hello%20TravelOrio!%20I%20have%20a%20question%20about%20my%20booking." class="link-arrow" target="_blank" rel="noopener">{{ __('Message about my booking') }} <span aria-hidden="true">→</span></a>
          </div>

          <div class="side-card">
            <h3>{{ __('Follow us') }}</h3>
            <div class="footer__social side-card__social">
              <a href="#" aria-label="{{ __('Facebook') }}">{{ __('Facebook') }}</a>
              <a href="#" aria-label="{{ __('Instagram') }}">{{ __('Instagram') }}</a>
              <a href="#" aria-label="{{ __('YouTube') }}">{{ __('YouTube') }}</a>
            </div>
          </div>

        </aside>

      </div>
    </section>


    <!-- =====================================================
         4. VISIT US / MAP
         ===================================================== -->
    <section class="section" id="visit">
      <div class="container">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Visit us') }}</p>
          <h2 class="section__title">{{ __('Our Dhaka office') }}</h2>
        </header>

        <div class="map-card" data-reveal>
          <div class="map-card__info">
            <h3>{{ __('TravelOrio HQ') }}</h3>
            <address>
              {{ __('House 12, Road 5') }}<br>
              {{ __('Dhanmondi, Dhaka 1205') }}<br>
              {{ __('Bangladesh') }}
            </address>
            <ul class="map-card__list">
              <li>{{ __('Easy to reach by CNG or ride-share') }}</li>
              <li>{{ __('Please book a time so a planner is free') }}</li>
            </ul>
            <a href="https://www.openstreetmap.org/?mlat=23.7461&amp;mlon=90.3742#map=16/23.7461/90.3742" class="btn btn--outline" target="_blank" rel="noopener">{{ __('Open in Maps') }}</a>
          </div>
          <div class="map-card__map">
            <iframe title="{{ __('Map showing the TravelOrio office in Dhanmondi, Dhaka') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              src="https://www.openstreetmap.org/export/embed.html?bbox=90.3642%2C23.7401%2C90.3842%2C23.7521&amp;layer=mapnik&amp;marker=23.7461%2C90.3742"></iframe>
          </div>
        </div>
      </div>
    </section>


    <!-- =====================================================
         5. QUICK FAQ
         ===================================================== -->
    <section class="section section--alt" id="faq">
      <div class="container container--narrow">
        <header class="section__header" data-reveal>
          <p class="eyebrow">{{ __('Quick answers') }}</p>
          <h2 class="section__title">{{ __('Before you write') }}</h2>
        </header>

        <div class="faq" data-reveal>
          <details class="faq__item">
            <summary>{{ __('How fast will you reply?') }}</summary>
            <p>{{ __('WhatsApp messages are usually answered within an hour during opening hours. Emails are answered within one working day.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Can I get a quote without paying anything?') }}</summary>
            <p>{{ __('Yes. Quotes are free, with no obligation. Payment is only requested once you confirm your plan.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Do you arrange trips for companies and large groups?') }}</summary>
            <p>{{ __('Yes. We plan corporate retreats, family reunions and school tours. Select "Group or corporate tour" in the form above.') }}</p>
          </details>
          <details class="faq__item">
            <summary>{{ __('Do you speak English as well as Bangla?') }}</summary>
            <p>{{ __('Our planners and guides speak both, and we can arrange guides in other languages on request.') }}</p>
          </details>
        </div>
      </div>
    </section>

  
@endsection

@push('scripts')
  <script src="{{ asset_js('contact.js') }}" defer></script>
@endpush
