@extends('layouts.app', ['title' => 'Page expired'])

@section('content')
<section class="section">
  <div class="container" style="text-align:center;padding-top:calc(var(--nav-h) + 3rem)">
    <p class="eyebrow">419</p>
    <h1 class="section__title">{{ __('Page expired') }}</h1>
    <p class="section__lead" style="margin-inline:auto">{{ __('Your session expired. Please reload the page and try again.') }}</p>
    <p style="margin-top:1.5rem"><a href="{{ lroute('home') }}" class="btn btn--primary btn--lg">{{ __('Back to home') }}</a></p>
  </div>
</section>
@endsection
