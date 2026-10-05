<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Applies the saved theme before first paint (prevents a flash). The language comes from the URL. --}}
<script>(function(){var d=document.documentElement;d.classList.add('js');try{var t=localStorage.getItem('travelorio-theme');if(t==='dark'||(!t&&window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)){d.setAttribute('data-theme','dark');}}catch(e){}})();</script>

@php
  // Titles and descriptions are written in English in the views and translated here.
  $metaTitle = $fullTitle ?? __(($title ?? null) ? $title.' | '.site('name') : site('name').' | '.site('tagline'));
  $metaDescription = __($description ?? "Book curated tours to Cox's Bazar, Sundarbans, Sylhet, Bandarban, Saint Martin and Kuakata with local guides and transparent pricing.");
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="theme-color" content="#0a6ea8">
<link rel="canonical" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en" href="{{ alternate_url('en') }}">
<link rel="alternate" hreflang="bn" href="{{ alternate_url('bn') }}">
<link rel="alternate" hreflang="x-default" href="{{ alternate_url('en') }}">

<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ site('name') }}">
<meta property="og:locale" content="{{ is_bn() ? 'bn_BD' : 'en_US' }}">
<meta property="og:locale:alternate" content="{{ is_bn() ? 'en_US' : 'bn_BD' }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@500;600;700&display=swap" rel="stylesheet">

@vite('resources/css/travelorio.css')
