<div class="min-h-[calc(100vh-44px)] bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#2c6fad]">Planos</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Escolha a subscrição certa</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Atualize ou ative o seu plano para desbloquear os módulos certos para a sua equipa.</p>
            </div>
            <a href="{{ route('subscription.dashboard') }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Voltar a subscrição
            </a>
        </div>

        @if (session('warning'))
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
                {{ session('warning') }}
            </div>
        @endif

        @error('selectedPlanId')
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                Escolha um plano para continuar.
            </div>
        @enderror

        <div class="grid gap-5 lg:grid-cols-3">
            @foreach ($plans as $plan)
                @php
                    $price = $plan->currentPrice();
                    $isSelected = $selectedPlanId == $plan->id;
                    $isPopular = $plan->code === 'baoba';
                    $highlights = [
                        'ubuntu' => ['Pequenos negócios', 'fa-seedling', 'bg-slate-100 text-slate-700'],
                        'baoba' => ['Empresas em crescimento', 'fa-tree', 'bg-blue-100 text-[#2c6fad]'],
                        'leao' => ['Operações estabelecidas', 'fa-crown', 'bg-amber-100 text-amber-700'],
                    ][$plan->code] ?? ['Plano Khuma', 'fa-layer-group', 'bg-slate-100 text-slate-700'];
                @endphp

                <article class="relative flex flex-col rounded-xl border bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl {{ $isSelected ? 'border-[#2c6fad] ring-4 ring-blue-100' : ($isPopular ? 'border-[#2c6fad]' : 'border-slate-200') }}">
                    @if($isPopular)
                        <span class="absolute right-5 top-5 rounded-full bg-[#2c6fad] px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">Popular</span>
                    @endif

                    <div class="mb-6">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg {{ $highlights[2] }}">
                            <i class="fa-solid {{ $highlights[1] }}"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-950">{{ $plan->name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $highlights[0] }}</p>
                    </div>

                    <div class="mb-6">
                        @if($price)
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-bold tracking-tight text-slate-950">{{ number_format($price->amount, 0) }}</span>
                                <span class="pb-1 text-sm font-semibold text-slate-500">{{ $price->currency }}/mês + IVA</span>
                            </div>
                        @else
                            <span class="text-3xl font-bold tracking-tight text-slate-950">Sob consulta</span>
                        @endif
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $plan->description }}</p>
                    </div>

                    <ul class="mb-6 flex-1 space-y-3">
                        @foreach($plan->features as $feature)
                            @if($plan->hasFeature($feature->feature_key))
                                <li class="flex items-start gap-3 text-sm text-slate-700">
                                    <span class="mt-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                    <span>
                                        <span class="font-medium">{{ $plan->featureLabel($feature->feature_key) ?? str($feature->feature_key)->replace('_', ' ')->title() }}</span>
                                        @if(!in_array($feature->feature_value, ['1', 'true'], true))
                                            <span class="text-slate-500">: {{ $feature->feature_value }}</span>
                                        @endif
                                    </span>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <button wire:click="selectPlan({{ $plan->id }})"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-bold transition {{ $isSelected ? 'bg-[#2c6fad] text-white' : 'border border-[#2c6fad] bg-white text-[#2c6fad] hover:bg-blue-50' }}">
                        <i class="fa-solid {{ $isSelected ? 'fa-circle-check' : 'fa-plus' }} text-xs"></i>
                        {{ $isSelected ? 'Selecionado' : 'Escolher plano' }}
                    </button>
                </article>
            @endforeach
        </div>

        <div class="mt-8 flex flex-col-reverse items-center justify-center gap-3 sm:flex-row">
            <button wire:click="skipForNow" class="rounded-lg px-5 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                Ignorar por agora
            </button>

            @if ($selectedPlanId)
                <button wire:click="continue"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2c6fad] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#245b8e]">
                    Continuar para pagamento
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            @endif
        </div>
    </div>
</div>
