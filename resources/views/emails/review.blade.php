<h2 style="margin:0 0 12px">New review to moderate</h2>
<table cellpadding="6" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:15px">
  <tr><td><b>Name</b></td><td>{{ $review->getTranslation('name', 'en') }}</td></tr>
  <tr><td><b>Destination</b></td><td>{{ $review->destination?->getTranslation('name', 'en') }}</td></tr>
  <tr><td><b>Rating</b></td><td>{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</td></tr>
  <tr><td valign="top"><b>Review</b></td><td>{!! nl2br(e($review->getTranslation('body', 'en'))) !!}</td></tr>
</table>
<p>It stays hidden until it is approved.</p>
