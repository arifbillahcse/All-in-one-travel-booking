<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  @include('partials.head')
</head>
<body class="{{ $bodyClass ?? '' }}">

  <a class="skip-link" href="#main">{{ __('Skip to content') }}</a>

  @include('partials.navbar')

  <main id="main" {!! $mainAttrs ?? '' !!}>
    @yield('content')
  </main>

  @include('partials.footer')

  @include('partials.floats')

  {{-- Pages are rendered by Laravel. These scripts only add behaviour (forms, estimate, lightbox, ...).
       bn.js + core.js give them translated messages (TO.t) for the page language. --}}
  <script nonce="{{ csp_nonce() }}">window.TRAVELORIO_ROUTES = { destination: @json(lurl('destinations')), packages: @json(lurl('packages')), blog: @json(lurl('blog')) };</script>
  <script src="{{ asset_js('i18n/bn.js') }}" defer></script>
  <script src="{{ asset_js('i18n/core.js') }}" defer></script>
  @stack('scripts')
  <script src="{{ asset_js('main.js') }}" defer></script>
</body>
</html>
