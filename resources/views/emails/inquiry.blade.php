@php($i = $inquiry)
<h2 style="margin:0 0 12px">{{ $i->type === 'booking' ? 'New booking request' : 'New contact message' }}</h2>
<table cellpadding="6" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:15px">
  <tr><td><b>Name</b></td><td>{{ $i->name }}</td></tr>
  <tr><td><b>Phone</b></td><td>{{ $i->phone }} (<a href="https://wa.me/{{ ltrim($i->phone, '+') }}">WhatsApp</a>)</td></tr>
  @if ($i->email)<tr><td><b>Email</b></td><td>{{ $i->email }}</td></tr>@endif
  @if ($i->destination)<tr><td><b>Destination</b></td><td>{{ $i->destination->getTranslation('name', 'en') }}</td></tr>@endif
  @if ($i->package)<tr><td><b>Package</b></td><td>{{ $i->package->getTranslation('name', 'en') }}</td></tr>@endif
  @if ($i->topic)<tr><td><b>Topic</b></td><td>{{ $i->topic }}</td></tr>@endif
  @if ($i->travel_date)<tr><td><b>Travel date</b></td><td>{{ $i->travel_date->format('F j, Y') }}</td></tr>@endif
  @if ($i->guests)<tr><td><b>Travelers</b></td><td>{{ $i->guests }}</td></tr>@endif
  @if ($i->estimated_total)<tr><td><b>Estimate</b></td><td>৳{{ number_format($i->estimated_total) }}</td></tr>@endif
  @if ($i->message)<tr><td valign="top"><b>Message</b></td><td>{!! nl2br(e($i->message)) !!}</td></tr>@endif
  <tr><td><b>Language</b></td><td>{{ strtoupper($i->locale) }}</td></tr>
</table>
