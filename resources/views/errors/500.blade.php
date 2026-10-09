@extends('layouts.app', ['title' => 'Server error'])

@section('content')
<section class="section">
  <div class="container" style="text-align:center;padding-top:calc(var(--nav-h) + 3rem)">
    <p class="eyebrow">500</p>
    <h1 class="section__title">{{ __('Something went wrong') }}</h1>
    <p class="section__lead" style="margin-inline:auto">{{ __('Please try again in a moment, or message us on WhatsApp.') }}</p>
    <p style="margin-top:1.5rem"><a href="{{ lroute('home') }}" class="btn btn--primary btn--lg">{{ __('Back to home') }}</a></p>
  </div>
</section>
@endsection
