<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Khuma CRM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_#dbeafe,_transparent_32%),linear-gradient(135deg,_#f8fafc_0%,_#eef6ff_48%,_#f8fafc_100%)] px-4 py-8">
            <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-6xl items-center justify-center">
                <div class="hidden flex-1 pr-12 lg:block">
                    <a href="/" class="inline-flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#2c6fad] text-lg font-black text-white shadow-lg">K</span>
                        <span class="text-xl font-bold tracking-tight text-slate-950">Khuma CRM</span>
                    </a>
                    <h1 class="mt-10 max-w-lg text-4xl font-bold tracking-tight text-slate-950">Organize vendas, WhatsApp e equipas num CRM mais simples.</h1>
                    <p class="mt-4 max-w-md text-base leading-7 text-slate-600">Crie a conta, escolha um plano quando estiver pronto e acompanhe tudo num painel operacional.</p>
                    <div class="mt-8 grid max-w-md grid-cols-2 gap-3">
                        <div class="rounded-lg border border-white/70 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-sm font-bold text-slate-900">CRM</p>
                            <p class="mt-1 text-xs text-slate-500">Pipeline, leads e clientes.</p>
                        </div>
                        <div class="rounded-lg border border-white/70 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-sm font-bold text-slate-900">Subscrições</p>
                            <p class="mt-1 text-xs text-slate-500">Planos e acessos por módulo.</p>
                        </div>
                    </div>
                </div>

                <div class="w-full max-w-xl">
                    <div class="mb-6 flex items-center justify-center lg:hidden">
                        <a href="/" class="inline-flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#2c6fad] font-black text-white shadow-lg">K</span>
                            <span class="text-lg font-bold tracking-tight text-slate-950">Khuma CRM</span>
                        </a>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-white/70 bg-white/90 p-6 shadow-2xl shadow-blue-950/10 backdrop-blur sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
