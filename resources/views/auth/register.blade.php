<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold tracking-tight text-slate-950">Criar conta</h2>
        <p class="mt-1 text-sm text-slate-500">Comece com os dados da empresa e escolha uma subscrição, ou ignore por agora.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Nome')" />
                <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="company_name" :value="__('Empresa')" />
                <x-text-input id="company_name" class="mt-1 block w-full" type="text" name="company_name" :value="old('company_name')" required autocomplete="organization" />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Numero de celular')" />
                <x-text-input id="phone" class="mt-1 block w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" inputmode="numeric" placeholder="84xxxxxxx" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="mt-5">
            <x-input-label :value="__('Escolha uma subscrição')" />
            <p class="mt-1 text-xs text-gray-500">Pode escolher agora para ir direto ao pagamento, ou ignorar e escolher mais tarde.</p>

            <div class="mt-3 grid gap-3">
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 transition hover:border-[#2c6fad]">
                    <input type="radio" name="plan_id" value="" @checked(old('plan_id') === null || old('plan_id') === '') class="mt-1 text-[#2c6fad]">
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">Ignorar por agora</span>
                        <span class="block text-xs text-gray-500">A conta será criada, mas os módulos pagos ficam bloqueados até ativar uma subscrição.</span>
                    </span>
                </label>

                @foreach ($plans as $plan)
                    @php $price = $plan->currentPrice(); @endphp
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 transition hover:border-[#2c6fad]">
                        <input type="radio" name="plan_id" value="{{ $plan->id }}" @checked((string) old('plan_id') === (string) $plan->id) class="mt-1 text-[#2c6fad]">
                        <span class="flex-1">
                            <span class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold text-gray-800">{{ $plan->name }}</span>
                                <span class="text-sm font-bold text-[#2c6fad]">
                                    {{ $price ? number_format($price->amount, 0) . ' ' . $price->currency . '/mês' : 'Sob consulta' }}
                                </span>
                            </span>
                            <span class="mt-1 block text-xs text-gray-500">{{ $plan->description }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('plan_id')" class="mt-2" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="password" :value="__('Senha')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <a class="text-sm font-medium text-slate-500 hover:text-[#2c6fad]" href="{{ route('login') }}">
                {{ __('Já está registado?') }}
            </a>

            <x-primary-button class="justify-center bg-[#2c6fad] px-5 py-2.5 hover:bg-[#245b8e]">
                {{ __('Registar-se') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
