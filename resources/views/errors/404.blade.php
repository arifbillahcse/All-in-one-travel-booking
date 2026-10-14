@extends('layouts.app', ['title' => 'Page not found'])

@section('content')
<section class="section">
  <div class="container" style="text-align:center;padding-top:calc(var(--nav-h) + 3rem)">
    <p class="eyebrow">404</p>
    <h1 class="section__title">{{ __('We could not find that page') }}</h1>
    <p class="section__lead" style="margin-inline:auto">{{ __('The link may be old or mistyped. Let us get you back on the road.') }}</p>
    <p style="margin-top:1.5rem"><a href="{{ lroute('home') }}" class="btn btn--primary btn--lg">{{ __('Back to home') }}</a></p>
  </div>
</section>
@endsection
