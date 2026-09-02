<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold tracking-tight text-slate-950">Entrar</h2>
        <p class="mt-1 text-sm text-slate-500">Acesse o seu CRM e acompanhe vendas, equipa e subscrição.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Lembre-se') }}</span>
            </label>
        </div>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-slate-500 hover:text-[#2c6fad]" href="{{ route('password.request') }}">
                    {{ __('Esqueceu a sua palavra-passe?') }}
                </a>
            @endif

            <div class="flex items-center gap-3">
                <a href="{{ route('register') }}" class="text-sm font-semibold text-[#2c6fad] hover:text-[#245b8e]">Criar conta</a>
                <x-primary-button class="bg-[#2c6fad] px-5 py-2.5 hover:bg-[#245b8e]">
                    {{ __('Iniciar sessão') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
