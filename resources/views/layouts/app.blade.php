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

  {{-- Order matters: data -> translations -> language engine -> page script -> shared behaviour.
       These legacy scripts are replaced by server-side code in later phases. --}}
  <script>window.TRAVELORIO_ROUTES = { destination: @json(lurl('destinations')), packages: @json(lurl('packages')), blog: @json(lurl('blog')) };</script>
  @stack('scripts-data')
  <script src="{{ asset_js('i18n/bn.js') }}" defer></script>
  @stack('scripts-i18n')
  <script src="{{ asset_js('i18n/core.js') }}" defer></script>
  @stack('scripts')
  <script src="{{ asset_js('main.js') }}" defer></script>
</body>
</html>
