<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Detalhes da Empresa
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Actualize aqui os detalhes da sua empresa como endereco e NUIT
        </p>
    </header>

    <form method="post" action="#" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->company->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="tax_number" value="NUIT" />
            <x-text-input id="tax_number" name="tax_number" type="number" class="mt-1 block w-full" :value="old('tax_number', $user->company->tax_number)" required />
            <x-input-error class="mt-2" :messages="$errors->get('tax_number')" />
        </div>

        <div>
            <x-input-label for="address" value="Address" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->company->address)" required autofocus autocomplete="address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->company->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'company-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
