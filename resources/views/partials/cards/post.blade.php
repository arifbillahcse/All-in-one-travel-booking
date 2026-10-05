@php
  $isFeatured = $isFeatured ?? false;
  [$w, $h] = $isFeatured ? [1000, 700] : [720, 480];
  $href = lroute('blog.post', $post->slug);
@endphp
<article class="blog-card{{ $isFeatured ? ' blog-card--featured' : '' }}">
  <a class="blog-card__media" href="{{ $href }}" tabindex="-1" aria-hidden="true">
    <img src="{{ placeholder_image('blog-'.$post->slug, $w, $h) }}" alt="" loading="lazy" width="{{ $w }}" height="{{ $h }}">
    <span class="blog-card__cat">{{ $post->category->name }}</span>
  </a>
  <div class="blog-card__body">
    <p class="blog-card__meta">{{ format_date($post->published_at) }} · {{ t('{n} min read', ['n' => to_locale_digits($post->read_minutes)]) }}</p>
    <h3 class="blog-card__title"><a href="{{ $href }}">{{ $post->title }}</a></h3>
    <p class="blog-card__excerpt">{{ $post->excerpt }}</p>
    <a class="link-arrow" href="{{ $href }}">{{ __('Read article') }} <span aria-hidden="true">→</span></a>
  </div>
</article>
