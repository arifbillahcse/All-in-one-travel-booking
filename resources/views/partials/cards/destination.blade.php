@php($href = lroute('destination', $destination->slug))
<article class="card destination" data-reveal>
  <a href="{{ $href }}" class="card__media" aria-label="{{ t('View {name} trip details', ['name' => $destination->name]) }}">
    @php($photo = $destination->cardImage())
    <img {{ $photo?->attributes('(min-width: 900px) 33vw, (min-width: 600px) 50vw, 100vw', [800, 600]) }} alt="{{ $destination->name }}" loading="lazy">
    <span class="card__tag">{{ t('From {price}', ['price' => format_money($destination->price_from)]) }}</span>
  </a>
  <div class="card__body">
    <p class="card__meta">{{ $destination->region }} · {{ $destination->duration }}</p>
    <h3 class="card__title">{{ $destination->name }}</h3>
    @isset($text)
    <p class="card__text">{{ $destination->{$text} }}</p>
    @endisset
    <a href="{{ $href }}" class="link-arrow">{{ __($linkLabel ?? 'View trip details') }} <span aria-hidden="true">→</span></a>
  </div>
</article>
