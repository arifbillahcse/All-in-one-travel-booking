<figure class="review review--card" data-reveal>
  <div class="review__top">
    <div class="review__stars" role="img" aria-label="{{ t('{n} out of 5 stars', ['n' => to_locale_digits($review->rating)]) }}">{{ stars($review->rating) }}</div>
    <span class="review__verified">{{ __('Verified traveler') }}</span>
  </div>
  <h3 class="review__title">{{ $review->title }}</h3>
  <blockquote class="review__text">{{ $review->body }}</blockquote>
  <figcaption class="review__author">
    <span class="avatar" aria-hidden="true">{{ initials($review->name) }}</span>
    <span><strong>{{ $review->name }}</strong><small>{{ $review->city }} · {{ $review->destination?->name }} · @if(! empty($showType) && $review->traveler_type){{ __($review->traveler_type) }} · @endif{{ format_date($review->reviewed_on, 'M Y') }}</small></span>
  </figcaption>
</figure>
