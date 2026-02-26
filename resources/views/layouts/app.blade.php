<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kuma') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- DataTables core -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

        <!-- DataTables Tailwind (estilos adaptados) -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.tailwindcss.min.css">
        <script src="https://cdn.datatables.net/1.13.5/js/dataTables.tailwindcss.min.js"></script>

        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#3B82F6',
                            secondary: '#1E40AF',
                            accent: '#10B981',
                            dark: '#1F2937',
                            light: '#F9FAFB'
                        },
                        fontFamily: {
                            'poppins': ['Poppins', 'sans-serif']
                        }
                    }
                }
            }
        </script>
        <style>
            body {
                font-family: 'Poppins', sans-serif;
            }

            .hero-gradient {
                background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            }

            .feature-card:hover {
                transform: translateY(-5px);
                transition: transform 0.3s ease;
            }

            .pricing-card {
                transition: all 0.3s ease;
            }

            .pricing-card:hover {
                transform: scale(1.03);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            .pricing-card.popular {
                border: 2px solid #3B82F6;
            }
        </style>

        @php($title=$title ?? (config('app.name').' — monitorização de chamadas'))
        @php($description=$metaDescription ?? 'Khuma — registo e análise de chamadas com privacidade e simplicidade.')
        @php($canonical=url()->current())
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
        <link rel="stylesheet" href="http://erp.mazedeve.com/im_livechat/external_lib.css"/>

        <script type="text/javascript" src="http://erp.mazedeve.com/im_livechat/external_lib.js"></script>

        <script type="text/javascript" src="http://erp.mazedeve.com/im_livechat/loader/1"></script>
        <!-- Scripts -->
        {{-- No need to call for app.css or app.js from here cuz vite is already calling --}}
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @livewireScripts
    </body>

</html>
