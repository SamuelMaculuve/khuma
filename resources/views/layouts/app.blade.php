<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Khuma CRM') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <link rel="shortcut icon" href="{{ asset('favicon_io/favicon.ico') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif']}}}}</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        html, body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

{{-- ═══════════════════════════════════════════════════════
     TOP NAV — Odoo style
═══════════════════════════════════════════════════════ --}}
<nav class="bg-[#2c6fad] sticky top-0 z-50 h-11 flex items-center px-2 gap-1">

    {{-- App grid (modules picker) --}}
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open"
                class="w-9 h-9 flex items-center justify-center rounded text-white/80 hover:bg-black/20 hover:text-white transition">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <rect x="0" y="0" width="4" height="4" rx="0.5"/>
                <rect x="6" y="0" width="4" height="4" rx="0.5"/>
                <rect x="12" y="0" width="4" height="4" rx="0.5"/>
                <rect x="0" y="6" width="4" height="4" rx="0.5"/>
                <rect x="6" y="6" width="4" height="4" rx="0.5"/>
                <rect x="12" y="6" width="4" height="4" rx="0.5"/>
                <rect x="0" y="12" width="4" height="4" rx="0.5"/>
                <rect x="6" y="12" width="4" height="4" rx="0.5"/>
                <rect x="12" y="12" width="4" height="4" rx="0.5"/>
            </svg>
        </button>
        <div x-show="open" @click.away="open = false" x-cloak
             class="absolute left-0 top-10 w-72 bg-white border border-gray-200 rounded shadow-xl p-3 z-50">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-1 mb-2">Módulos</p>
            <div class="grid grid-cols-3 gap-1">
                @php $modules = [
                    ['label'=>'Dashboard',   'route'=>'dashboard',            'icon'=>'fa-house',        'bg'=>'bg-blue-100',   'ic'=>'text-blue-600'],
                    ['label'=>'CRM',         'route'=>'leads.all',            'icon'=>'fa-chart-line',   'bg'=>'bg-indigo-100', 'ic'=>'text-indigo-600'],
                    ['label'=>'Email',       'route'=>'email-campaigns.index','icon'=>'fa-envelope',     'bg'=>'bg-purple-100', 'ic'=>'text-purple-600'],
                    ['label'=>'WhatsApp',    'route'=>'instance.create',      'icon'=>'fa-comments',     'bg'=>'bg-green-100',  'ic'=>'text-green-600'],
                    ['label'=>'Configurações','route'=>'settings.index',      'icon'=>'fa-gear',         'bg'=>'bg-slate-100',  'ic'=>'text-slate-600'],
                    ['label'=>'Subscrição',  'route'=>'subscription.dashboard','icon'=>'fa-credit-card',  'bg'=>'bg-amber-100',  'ic'=>'text-amber-600'],
                ]; @endphp
                @foreach($modules as $m)
                <a href="{{ route($m['route']) }}" @click="open=false"
                   class="flex flex-col items-center gap-1.5 p-2 rounded hover:bg-gray-50 transition text-center">
                    <div class="w-10 h-10 {{ $m['bg'] }} rounded-lg flex items-center justify-center">
                        <i class="fa-solid {{ $m['icon'] }} {{ $m['ic'] }}"></i>
                    </div>
                    <span class="text-xs text-gray-600 font-medium leading-tight">{{ $m['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Logo --}}
    <a href="{{ route('dashboard') }}" class="text-white font-bold text-base tracking-tight px-2 shrink-0">
        Khuma<span class="font-normal text-white/60 text-sm"> CRM</span>
    </a>

    <div class="w-px h-5 bg-white/20 mx-1 shrink-0"></div>

    {{-- Primary nav links --}}
    <div class="flex items-center gap-0.5 overflow-x-auto flex-1">
        @php
        $navItems = [
            ['label'=>'Dashboard',       'route'=>'dashboard',             'match'=>['dashboard']],
            ['label'=>'CRM',             'route'=>'leads.all',             'match'=>['leads.*','lead.*']],
            ['label'=>'Email Marketing', 'route'=>'email-campaigns.index', 'match'=>['email-campaigns.*']],
            ['label'=>'WhatsApp',        'route'=>'instance.create',       'match'=>['instance.*']],
            ['label'=>'Subscrição',      'route'=>'subscription.dashboard', 'match'=>['subscription.*']],
        ];
        @endphp
        @foreach($navItems as $item)
            @php $active = collect($item['match'])->contains(fn($p)=>request()->routeIs($p)); @endphp
            <a href="{{ route($item['route']) }}"
               class="px-3 h-9 flex items-center text-sm font-medium rounded whitespace-nowrap transition
                      {{ $active ? 'bg-black/25 text-white' : 'text-white/80 hover:bg-black/15 hover:text-white' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
        @role('admin')
        <a href="{{ route('users.index') }}"
           class="px-3 h-9 flex items-center text-sm font-medium rounded whitespace-nowrap transition
                  {{ request()->routeIs('users.*') ? 'bg-black/25 text-white' : 'text-white/80 hover:bg-black/15 hover:text-white' }}">
            Utilizadores
        </a>
        <a href="{{ route('admin.plans') }}"
           class="px-3 h-9 flex items-center text-sm font-medium rounded whitespace-nowrap transition
                  {{ request()->routeIs('admin.plans') ? 'bg-black/25 text-white' : 'text-white/80 hover:bg-black/15 hover:text-white' }}">
            Planos
        </a>
        @endrole
    </div>

    {{-- Right: mail status + user menu --}}
    <div class="flex items-center gap-1 ml-auto shrink-0">


        <div class="w-px h-5 bg-white/20 mx-1"></div>

        {{-- Settings icon --}}
        <a href="{{ route('settings.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded text-white/80 hover:bg-black/20 hover:text-white transition
                  {{ request()->routeIs('settings.*') ? 'bg-black/25 text-white' : '' }}">
            <i class="fa-solid fa-gear text-sm"></i>
        </a>

        {{-- User dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2 px-2 h-9 rounded text-white/80 hover:bg-black/20 hover:text-white transition">
                <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold text-white shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <span class="text-sm font-medium hidden sm:block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute right-0 top-10 w-56 bg-white border border-gray-200 rounded shadow-xl z-50 py-1">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    @if(auth()->user()?->company)
                        <p class="text-xs text-[#2c6fad] font-medium mt-0.5">{{ auth()->user()->company->name }}</p>
                    @endif
                </div>
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-user w-4 text-gray-400"></i> Perfil
                </a>
                <a href="{{ route('settings.index') }}"
                   class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-gear w-4 text-gray-400"></i> Configurações
                </a>
                <a href="{{ route('subscription.dashboard') }}"
                   class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-credit-card w-4 text-gray-400"></i> Minha subscrição
                </a>
                <a href="{{ route('subscription.plans') }}"
                   class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-layer-group w-4 text-gray-400"></i> Planos
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left">
                        <i class="fa-solid fa-right-from-bracket w-4"></i> Terminar sessão
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════════════
     PAGE CONTENT
═══════════════════════════════════════════════════════ --}}
<main class="min-h-[calc(100vh-44px)] bg-slate-50">
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
