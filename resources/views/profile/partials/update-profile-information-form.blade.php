@if($user->company)
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
                <x-text-input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="mt-1 block w-full" 
                    :value="old('name', $user->company->name)" 
                    required 
                    autofocus 
                    autocomplete="name" 
                />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="tax_number" value="NUIT" />
                <x-text-input 
                    id="tax_number" 
                    name="tax_number" 
                    type="text" 
                    class="mt-1 block w-full" 
                    :value="old('tax_number', $user->company->tax_number)" 
                    required 
                />
                <x-input-error class="mt-2" :messages="$errors->get('tax_number')" />
            </div>

            <div>
                <x-input-label for="address" value="Address" />
                <x-text-input 
                    id="address" 
                    name="address" 
                    type="text" 
                    class="mt-1 block w-full" 
                    :value="old('address', $user->company->address)" 
                    required 
                    autofocus 
                    autocomplete="address" 
                />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="mt-1 block w-full" 
                    :value="old('email', $user->company->email)" 
                    required 
                    autocomplete="username" 
                />
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
@else
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Detalhes da Empresa
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Actualize aqui os detalhes da sua empresa como endereco e NUIT
            </p>
        </header>

        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        {{ __('Você ainda não possui uma empresa associada.') }}
                    </p>
                    <p class="text-sm text-yellow-700 mt-1">
                        {{ __('Por favor, entre em contato com o suporte para associar sua empresa.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endif