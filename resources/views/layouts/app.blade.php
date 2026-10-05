<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.head')
</head>
<body class="{{ $bodyClass ?? '' }}">

  <a class="skip-link" href="#main">Skip to content</a>

  @include('partials.navbar')

  <main id="main">
    @yield('content')
  </main>

  @include('partials.footer')

  @include('partials.floats')

  {{-- Order matters: translations -> language engine -> page scripts -> shared behaviour.
       The legacy scripts are replaced by server-side code in later phases. --}}
  <script src="{{ asset_js('i18n/bn.js') }}" defer></script>
  <script src="{{ asset_js('i18n/core.js') }}" defer></script>
  @stack('scripts')
  <script src="{{ asset_js('main.js') }}" defer></script>
</body>
</html>
