@props([
  'title' => config('app.name'),
  'description' => 'Khuma — registo e análise de chamadas com privacidade e simplicidade.',
  'canonical' => url()->current(),
  'image' => asset('assets/img/khuma-cover.png'),
  'locale' => 'pt-PT',
  'siteName' => config('app.name'),
])

<title>{{ $title }}</title>

<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}"/>

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#4f46e5">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="website">
<meta property="og:locale" content="{{ $locale }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:alt" content="{{ $siteName }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

{{-- Hreflang (ex.: Portugal e Moçambique) --}}
<link rel="alternate" hreflang="pt-PT" href="{{ $canonical }}">
<link rel="alternate" hreflang="pt-MZ" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

{{-- Favicons & PWA --}}
<link rel="shortcut icon" href="{{ asset('favicon_io/favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon_io/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon_io/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon_io/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

{{-- JSON-LD: Organização + Website + App --}}
<script type="application/ld+json">
    {
      "@context":"https://schema.org",
      "@type":"Organization",
      "name":"Khuma",
      "url":"{{ url('/') }}",
  "logo":"{{ asset('assets/img/logo-khuma.png') }}",
  "sameAs":[]
}
</script>
<script type="application/ld+json">
    {
      "@context":"https://schema.org",
      "@type":"WebSite",
      "name":"Khuma",
      "url":"{{ url('/') }}",
  "potentialAction":{
    "@type":"SearchAction",
    "target":"{{ url('/buscar') }}?q={search_term_string}",
    "query-input":"required name=search_term_string"
  }
}
</script>
<script type="application/ld+json">
    {
      "@context":"https://schema.org",
      "@type":"SoftwareApplication",
      "name":"Khuma",
      "applicationCategory":"BusinessApplication",
      "operatingSystem":"Android, iOS, Web",
      "offers":{"@type":"Offer","price":"0","priceCurrency":"MZN"}
    }
</script>
